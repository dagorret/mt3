# Documento Técnico: Modelo de Estados y Transiciones en Servicios Informáticos

---

### a) ¿Qué es un Estado?

Un **Estado** representa la situación o condición formal en la que se encuentra un objeto o proceso en un momento determinado dentro de su ciclo de vida.

En nuestro sistema, un estado no es un simple texto informativo para pintar un botón en la pantalla. Es una **representación del nivel de compromiso, responsabilidad y avance operativo** en el que se encuentra un `Ticket`, una `Tarea`, un `Préstamo` o un `Evento`.

---

### b) ¿Por qué usar Estados?

El uso de un modelo formal de estados resuelve los problemas estructurales de la gestión informal:

* **Determinismo:** El sistema conoce en todo momento qué se puede y qué no se puede hacer con un registro.

* **Trazabilidad e Inmutabilidad:** Permite saber con exactitud quién cambió la condición de un caso, cuándo lo hizo, bajo qué rol y por qué motivo (alimentando la Bitácora).

* **Gobernanza y Responsabilidad:** Garantiza que las responsabilidades no queden en el aire. Cada cambio de estado formaliza un traspaso de custodia, de asignación o de decisión.

* **Previsibilidad para la Analítica:** Permite medir tiempos reales de atención, demoras por bloqueos e indicadores de cumplimiento sin ambigüedades.

---

### c) Ejemplos de Estados

#### En el ciclo de vida de una Tarea (`TareaState`):

* **`Pendiente`:** Registrada en el sistema, aún sin planificación ni ejecución iniciada.

* **`Planificada`:** Con responsable nominal, recursos o fecha de compromiso asignados.

* **`EnEjecucion`:** El agente se encuentra realizando el trabajo técnico.

* **`Bloqueada`:** La ejecución se detuvo por un factor externo o falta de insumos (exige justificación).

* **`Postergada`:** Reprogramada formalmente en el tiempo con sustento.

* **`Cumplida`:** Trabajo finalizado en tiempo y con calidad técnica.

* **`Incumplida`:** Trabajo finalizado pero fuera de término, incompleto o no satisfactorio.

* **`Cerrada` / `Cancelada`:** Término formal del ciclo de la tarea con causa documentada.

#### En el ciclo de vida de un Ticket (`TicketState`):

* **`EnBandeja`:** Solicitud ingresada sin asignar.

* **`Tomado`:** Asumido por un agente o autoridad (dispara la creación de su Tarea derivada).

* **`EnProceso`:** Tareas operativas asociadas en marcha.

* **`NoPuedoResolver`:** Declaración formal de imposibilidad técnica o material.

* **`Elevado`:** Transferido jerárquicamente a Subjefatura, Jefatura o Coordinación.

* **`Cerrado`:** Caso resuelto y notificado.

---

### d) Transiciones

Una **Transición** es el paso controlado y válido desde un *Estado Origen* hacia un *Estado Destino*.

Las transiciones no son instantáneas ni libres; son **eventos del dominio** que pueden exigir requisitos previos:

* Validación de permisos y rol activo.

* Carga obligatoria de datos (motivo, responsable, archivos de evidencia).

* Ejecución de efectos secundarios (crear una tarea automática, cambiar un responsable, escribir en la Bitácora).

---

### e) Facilitaciones y Prohibiciones

El motor de estados actúa como el guardián de las reglas de negocio institucionales:

#### Facilitaciones (Lo que el motor simplifica y automatiza):

* **Automatización de reglas:** Al pasar un Ticket a `Tomado`, la transición genera la Tarea inicial sin que el usuario tenga que acordarse de hacerlo.

* **Auditoría automática:** Cada transición exitosa escribe de forma transparente en la Bitácora el cambio de estado, el autor real y el momento exacto.

* **Claridad en la Interfaz (UI):** La pantalla solo muestra al agente los botones de las transiciones que son válidas para el estado actual.

#### Prohibiciones (Lo que el motor impide estrictamente):

* **Saltos inválidos:** Se prohíbe pasar un Ticket directamente de `EnBandeja` a `Cerrado` sin haber sido `Tomado` o procesado mediante una Tarea.

* **Cierres opacos:** Se prohíbe cerrar una Tarea en `Incumplida` o `Cancelada` si el usuario no ingresa la `causa_cierre` justificativa.

* **Modificación de la historia:** Se prohíbe alterar o borrar transiciones pasadas (principio de inmutabilidad).

---

### g) Implementación con el paquete Spatie (`spatie/laravel-model-states`)

El paquete de Spatie mapea este modelo conceptual directamente en clases PHP limpias sin recargar la base de datos.

#### 1. Mapeo en Base de Datos

En la migración de la tabla (ej. `tickets` o `tareas`), la base de datos solo requiere una columna de texto simple:

```php
$table->string('estado');
```

#### 2. La Clase Base del Estado (`TicketState.php`)

Define los estados predeterminados y el mapa de transiciones permitidas:

```php
namespace App\States\Ticket;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class TicketState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(EnBandeja::class)
            // Transiciones simples permitidas:
            ->allowTransition(EnBandeja::class, Tomado::class)
            ->allowTransition(Tomado::class, EnProceso::class)
            ->allowTransition(EnBandeja::class, NoPuedoResolver::class)
            ->allowTransition([EnProceso::class, Elevado::class], Cerrado::class)
            // Transición compleja con lógica de negocio encapsulada:
            ->allowTransition([Tomado::class, EnProceso::class], Elevado::class, TransicionAElevado::class);
    }
}
```

#### 3. Estados Individuales como Clases PHP

Cada estado posible es una clase que extiende de la clase base:

```php
// App/States/Ticket/EnBandeja.php
class EnBandeja extends TicketState { public static $name = 'en_bandeja'; }

// App/States/Ticket/Tomado.php
class Tomado extends TicketState { public static $name = 'tomado'; }

// App/States/Ticket/Elevado.php
class Elevado extends TicketState { public static $name = 'elevado'; }
```

#### 4. Transiciones Personalizadas con Lógica Directa (`TransicionAElevado.php`)

Cuando la transición requiere validar o guardar datos de gobernanza, se define su propia clase:

```php
namespace App\States\Ticket\Transitions;

use App\Models\Ticket;
use App\Models\Persona;

class TransicionAElevado
{
    public function __construct(
        private Ticket $ticket,
        private string $motivo,
        private Persona $autoridadDestino
    ) {}

    public function handle(): Ticket
    {
        // 1. Guardar datos de gobernanza exigidos por la regla
        $this->ticket->responsable_id = $this->autoridadDestino->id;
        $this->ticket->motivo_elevacion = $this->motivo;
        
        // 2. Cambiar la clase de estado
        $this->ticket->estado = new Elevado($this->ticket);
        $this->ticket->save();

        // El Observer/Trait Auditable registrará la acción en la Bitácora automáticamente
        return $this->ticket;
    }
}
```

#### 5. Vinculación al Modelo Eloquent (`Ticket.php`)

Le indicamos a Laravel que la columna `estado` debe castearse a la máquina de estados:

```php
use Spatie\ModelStates\HasStates;
use App\States\Ticket\TicketState;

class Ticket extends Model
{
    use HasStates;

    protected $casts = [
        'estado' => TicketState::class,
    ];
}
```

#### 6. Uso desde Controladores o Servicios

```php
// Intentar un salto no permitido lanza una excepción automática de Spatie:
$ticket->estado->transitionTo(Cerrado::class); // ❌ TransitionNotFound Exception

// Ejecutar una transición permitida con parámetros:
$ticket->state->transition(new TransicionAElevado($ticket, 'Requiere autorización de gasto', $subjefe)); // ✅ Ok
```
