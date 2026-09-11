# Documento Técnico: Arquitectura de Auditoría e Inmutabilidad (`Auditable`)

---

### 1. ¿Qué es el Sistema Auditable?

El módulo **Auditable** es el componente de software encargado de interceptar, empaquetar y registrar de forma automática y transparente cada evento relevante o modificación que sufren las entidades del sistema (`Tickets`, `Tareas`, `Personas`, `Unidades`).

Opera en el corazón de la capa de persistencia de Laravel (Eloquent Events) y garantiza que **ninguna acción de negocio ocurra de manera anónima, invisible o huérfana**.

---

### 2. ¿Por qué se Audita?

En la gestión de servicios informáticos institucionales, la transparencia técnica es un requisito de gobernanza. Se audita para resolver los siguientes pilares operacionales:

* **No Repudio:** Un agente, usuario o autoridad no puede negar haber ejecutado una acción, autorizado un cambio de estado o reasignado una responsabilidad.
* **Causalidad Operativa:** Permite reconstruir la historia completa de un caso. Ante una falla, demora o conflicto, el sistema responde con precisión: *¿Qué cambió? ¿Quién lo cambió? ¿Cuándo ocurrió? y ¿Por qué se justificó?*
* **Seguridad y Cumplimiento:** Detecta anomalías, accesos o modificaciones no autorizadas en el ciclo de vida de la información.
* **Soporte a la Analítica:** Aporta la materia prima de datos reales para calcular métricas de eficiencia, tiempos de respuesta y carga de trabajo real por sector.

---

### 3. ¿Qué es la Bitácora?

La **Bitácora** es el soporte físico en base de datos (`tabla: bitacora`) donde se almacenan los registros generados por el motor Auditable.

Inspirada en el cuaderno de navegación marítimo, la bitácora posee dos propiedades arquitectónicas fundamentales:

#### a) Formato JSONB y Estructura Polimórfica

Almacena el estado exacto de los datos **antes** (`datos_anteriores`) y **después** (`datos_nuevos`) del evento en campos JSON de alta velocidad de consulta, soportando cualquier tipo de modelo mediante relaciones polimórficas (`auditable_type` y `auditable_id`).

#### b) Inmutabilidad Estricta a Nivel de Motor (PostgreSQL)

A diferencia de las tablas comunes de la aplicación, la bitácora funciona bajo el principio **Append-Only** (solo lectura y anexado). Un **Trigger nativo en PostgreSQL** rechaza de forma absoluta cualquier instrucción `UPDATE` o `DELETE`:

```sql
-- Regla de Inmutabilidad en PostgreSQL
CREATE OR REPLACE FUNCTION prevenir_modificacion_bitacora()
RETURNS TRIGGER AS $$
BEGIN
    RAISE EXCEPTION 'Operación no permitida: La bitácora es inmutable y no acepta UPDATE ni DELETE.'
        USING ERRCODE = '45000';
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_bitacora_inmutable
BEFORE UPDATE OR DELETE ON bitacora
FOR EACH ROW
EXECUTE FUNCTION prevenir_modificacion_bitacora();
```

---

### 4. Implementación en Código

La implementación se sostiene sobre tres pilares: la **Migración**, el **Modelo Eloquent**, y el **Trait `Auditable**`.

#### A. Tabla de Base de Datos (`create_bitacoras_table.php`)

```php
Schema::create('bitacora', function (Blueprint $table) {
    $table->id();
    
    // Entidad Auditada (Polimórfico)
    $table->string('auditable_type');
    $table->unsignedBigInteger('auditable_id');
    $table->index(['auditable_type', 'auditable_id']);

    // Sujetos de la Acción
    $table->foreignId('persona_id')->nullable()->constrained('personas');
    $table->foreignId('user_id')->nullable()->constrained('users');

    // Evento y Justificación
    $table->string('tipo_evento'); // CREACION, MODIFICACION, ELIMINACION, CAMBIO_ESTADO
    $table->text('justificacion')->nullable();

    // Instantánea de Datos
    $table->jsonb('datos_anteriores')->nullable();
    $table->jsonb('datos_nuevos')->nullable();

    // Contexto de Red
    $table->string('ip_address', 45)->nullable();
    $table->text('user_agent')->nullable();

    // Marca de Tiempo
    $table->timestamp('created_at')->useCurrent();
});
```

#### B. Modelo de Dominio (`app/Models/Bitacora.php`)

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bitacora extends Model
{
    protected $table = 'bitacora';
    public const UPDATED_AT = null; // No permite updated_at

    protected $fillable = [
        'auditable_type', 'auditable_id', 'persona_id', 'user_id',
        'tipo_evento', 'justificacion', 'datos_anteriores', 'datos_nuevos',
        'ip_address', 'user_agent'
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'created_at' => 'datetime',
    ];

    public function auditable(): MorphTo { return $this->morphTo(); }
    public function persona(): BelongsTo { return $this->belongsTo(Persona::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
```

#### C. Trait de Automatización (`app/Traits/Auditable.php`)

Permite conectar cualquier modelo con la bitácora en una sola línea de código:

```php
namespace App\Traits;

use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(fn ($model) => $model->registrarEnBitacora('CREACION', null, $model->toArray()));

        static::updated(function ($model) {
            $cambios = $model->getChanges();
            $anteriores = array_intersect_key($model->getOriginal(), $cambios);

            if (!empty($cambios)) {
                $model->registrarEnBitacora('MODIFICACION', $anteriores, $cambios);
            }
        });

        static::deleted(fn ($model) => $model->registrarEnBitacora('ELIMINACION', $model->toArray(), null));
    }

    public function registrarEnBitacora(string $tipoEvento, ?array $anteriores = null, ?array $nuevos = null, ?string $justificacion = null): void
    {
        $user = Auth::user();
        $personaId = $user && method_exists($user, 'persona') ? $user->persona?->id : null;

        Bitacora::create([
            'auditable_type'   => get_class($this),
            'auditable_id'     => $this->getKey(),
            'persona_id'       => $personaId,
            'user_id'          => $user?->id,
            'tipo_evento'      => $tipoEvento,
            'justificacion'    => $justificacion ?? request('justificacion'),
            'datos_anteriores' => $anteriores,
            'datos_nuevos'     => $nuevos,
            'ip_address'       => Request::ip(),
            'user_agent'       => Request::userAgent(),
        ]);
    }

    public function bitacoras()
    {
        return $this->morphMany(Bitacora::class, 'auditable');
    }
}
```

#### D. Uso en los Modelos de Negocio

Para que un modelo comience a auditarse automáticamente, solo se debe incluir el Trait:

```php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use Auditable; // Auditoría automática activada
}
```
