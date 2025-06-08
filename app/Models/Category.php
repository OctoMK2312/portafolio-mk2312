<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categorys'; // Asegúrate de que el nombre de la tabla es correcto

    protected $fillable = [
        "name",
        "description",
        "slug",
        "parent_id",
    ];

    protected $casts = [
        "parent_id" => "integer",
    ];

    /**
     * Obtiene la categoría padre.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id'); // O Category::class
    }

    /**
     * Obtiene las categorías hijas (subcategorías directas).
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id'); // O Category::class
    }

    /**
     * Obtiene todas las subcategorías recursivamente.
     * Esto te permite obtener todas las subcategorías de una categoría, incluyendo las subcategorías de las subcategorías.
     */
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    // Podrías añadir también un accesor para obtener todas las subcategorías recursivamente
    // o para verificar si es una categoría raíz, etc.
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }
}