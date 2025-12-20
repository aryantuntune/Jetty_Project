<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'from_branch_id', 'to_branch_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function fromBranch() {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch() {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }
}