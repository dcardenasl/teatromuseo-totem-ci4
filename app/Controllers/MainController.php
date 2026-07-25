<?php

declare(strict_types=1);

namespace App\Controllers;

/**
 * Handles splash, language selector and main menu screens.
 */
final class MainController extends BaseTotemController
{
    public function index(): string
    {
        return view('totem/splash', $this->pageMeta(lang('Splash.welcome')));
    }

    public function language(): string
    {
        $from = $this->request->getGet('from');

        if (is_string($from) && $from !== '' && ! preg_match('/^[a-z0-9\/\-]+$/', $from)) {
            $from = '';
        }

        $onCancel = $from !== '' ? $from : 'menu';
        $onSelect = $from === '/' ? 'menu' : $onCancel;

        return view('totem/language', array_merge(
            $this->pageMeta(lang('Menu.select_language')),
            [
                'from' => $from,
                'onSelect' => $onSelect,
                'onCancel' => $onCancel,
            ]
        ));
    }

    public function mainMenu(): string
    {
        return view('totem/main_menu', array_merge(
            $this->pageMeta(lang('Nav.main_menu')),
            [
                'nav' => $this->shellNav(base_url('/')),
                'items' => [
                    $this->menuItem(lang('Menu.museum'), 'museo', lang('Menu.museum_copy'), 'menu-card--museum', 'menu/menu_museo.webp'),
                    $this->menuItem(lang('Menu.school'), 'teatro-escuela', lang('Menu.school_copy'), 'menu-card--school', 'menu/menu_escuela.webp'),
                    $this->menuItem(lang('Menu.programming'), 'cartelera', lang('Menu.programming_copy'), 'menu-card--programming', 'menu/menu_programacion.webp'),
                    $this->menuItem(lang('Menu.visits'), 'visitas-guiadas', lang('Menu.visits_copy'), 'menu-card--visits', 'menu/menu_visitas.webp'),
                    $this->menuItem(lang('Menu.friends'), 'amigos-de-teatromuseo', lang('Menu.friends_copy'), 'menu-card--friends', 'menu/menu_amigos.webp'),
                ],
            ]
        ));
    }

    /**
     * Friendly multi-language 404 handler.
     */
    public function notFound(): string
    {
        $this->response->setStatusCode(404);

        return view('totem/errors/not_found', array_merge(
            $this->pageMeta(lang('Common.error_404_title')),
            ['nav' => $this->shellNav(base_url('menu'))]
        ));
    }
}
