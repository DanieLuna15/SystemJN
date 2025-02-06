<?php

namespace App\Models;

use App\Constants\Status;
use App\Http\Traits\Searchable;
use App\Http\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Ministerio extends Model
{
    use HasFactory, Searchable,  GlobalStatus;

    protected $fillable = ['nombre', 'logo', 'multa_incremento', 'hora_tolerancia', 'status'];

    protected $table = 'ministerios';
    protected $primaryKey='id';
    public $timestamps=true;
    
    public function statusBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->status == Status::ACTIVE) {
                $html = '<span class="badge badge--success">' . trans("Active") . '</span>';
            } elseif ($this->status == Status::INACTIVE) {
                $html = '<span class="badge badge--danger">' . trans("Inactive") . '</span>';
            }
            return $html;
        });
    }
}
