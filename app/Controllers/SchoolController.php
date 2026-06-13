<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Presenters\SchoolPresenter;

/**
 * Handles the Teatro Escuela screen.
 */
final class SchoolController extends BaseTotemController
{
    public function theaterSchool(): string
    {
        $presenter = new SchoolPresenter();
        $context   = $presenter->present(
            $this->totemApi()->courses(),
            $this->request->getLocale(),
        );

        return view('totem/theater_school', array_merge(
            $this->pageMeta(lang('Menu.school')),
            [
                'nav' => $this->shellNav(),
                'section' => $context['section'],
                'courses' => $context['courses'],
                'teachers' => $context['teachers'],
                'students' => $context['students'],
                'personPhoto' => $context['personPhoto'],
            ]
        ));
    }
}
