<?php

namespace App\Models\Interfaces;

use App\Models\Board;

interface Commentable
{
    public function getBoard(): ?Board;   
}

?>