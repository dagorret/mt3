namespace App\Traits;

use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Boot del trait para registrar los observers de Eloquent.
     */
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->registrarEnBitacora('CREACION', null, $model->toArray());
        });

        static::updated(function ($model) {
            // Obtenemos solo las columnas que sufrieron cambios
            $cambios = $model->getChanges();
            $anteriores = array_intersect_key($model->getOriginal(), $cambios);

            // Evitamos registrar si no hubo cambios reales
            if (!empty($cambios)) {
                $model->registrarEnBitacora('MODIFICACION', $anteriores, $cambios);
            }
        });

        static::deleted(function ($model) {
            $model->registrarEnBitacora('ELIMINACION', $model->toArray(), null);
        });
    }

    /**
     * Método centralizado para guardar el registro de auditoría.
     */
    public function registrarEnBitacora(string $tipoEvento, ?array $anteriores = null, ?array $nuevos = null, ?string $justificacion = null): void
    {
        $user = Auth::user();
        // Intentamos obtener la persona vinculada al usuario autenticado si existe
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

    /**
     * Relación para obtener el historial de bitácora de este modelo.
     */
    public function bitacoras()
    {
        return $this->morphMany(Bitacora::class, 'auditable');
    }
}