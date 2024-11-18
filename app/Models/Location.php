<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'subcounty_id'];

    public function subcounty()
    {
        return $this->belongsTo(Subcounty::class);
    }

    public function sublocations()
    {
        return $this->hasMany(Sublocation::class);
    }
}
