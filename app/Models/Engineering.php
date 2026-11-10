<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Engineering extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'kategori',
        'subkategori', 
        'sub',
        'doc_part1',
        'doc_part2',
        'doc_part3',
        'doc_part4',
        'doc_part5',
        'doc_part6',
        'document_name',
        'approval_status'
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
