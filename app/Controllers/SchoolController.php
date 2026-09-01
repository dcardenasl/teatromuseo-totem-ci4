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
        $locale = $this->request->getLocale();
        $result = $this->totemApi()->courses($locale);
        $context = (new SchoolPresenter())->present($result, $locale);

        return view('totem/theater_school', array_merge(
            $this->pageMeta(lang('Menu.school')),
            [
                'nav' => $this->shellNav(),
                'unavailable' => $context['state'] === 'unavailable',
                'stale' => $context['state'] === 'stale',
                'section' => $context['section'],
                'courses' => $context['courses'],
                'teachers' => $context['teachers'],
                'personPhoto' => $context['personPhoto'],
            ]
        ));
    }
}
