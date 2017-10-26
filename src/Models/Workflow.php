<?php 

namespace HskyZhou\Workflow\Models;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    protected $fillable = [
        'name', 'model', 'desc', 'created_at', 'updated_at'
    ];

    public function places()
    {
    	return $this->hasMany(Place::class, 'workflow_id', 'id');
    }

    public function transitions()
    {
    	return $this->hasMany(Transition::class, 'workflow_id', 'id');
    }

    public function arcs()
    {
    	return $this->hasMany(Arc::class, 'workflow_id', 'id');
    }
}
