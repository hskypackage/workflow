<?php 

namespace HskyZhou\Workflow\Models;

use Illuminate\Database\Eloquent\Model;

class Arc extends Model
{
    protected $guarded = [];

    public function place()
    {
    	return $this->hasOne(Place::class, 'place_id', 'id');
    }

    public function transition()
    {
    	return $this->hasOne(Transition::class, 'id', 'transition_id');
    }

    public function placeFrom()
    {
    	return $this->hasOne(Place::class, 'id', 'from');
    }

    public function placeTo()
    {
    	return $this->hasOne(Place::class, 'id', 'to');
    }
}
