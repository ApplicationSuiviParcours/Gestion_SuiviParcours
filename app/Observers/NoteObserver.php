<?php

namespace App\Observers;

use App\Models\Note;

class NoteObserver
{
    public function saved(Note $note): void
    {
        $this->recalculerMoyenne($note);
    }

    public function deleted(Note $note): void
    {
        $this->recalculerMoyenne($note);
    }

    private function recalculerMoyenne(Note $note): void
    {
        $bulletin = $note->bulletin;

        if (!$bulletin) {
            return;
        }

        $total = 0;
        $coef  = 0;

        foreach ($bulletin->notes as $n) {
            $total += $n->valeur * $n->coefficient;
            $coef  += $n->coefficient;
        }

        $bulletin->update([
            'moyenne' => $coef > 0 ? round($total / $coef, 2) : 0,
        ]);
    }
}
