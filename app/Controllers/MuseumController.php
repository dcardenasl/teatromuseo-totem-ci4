<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Presenters\MuseumTodayPresenter;

/**
 * Handles museum section screens: menu, building, institution, today and
 * comic history.
 */
final class MuseumController extends BaseTotemController
{
    public function museum(): string
    {
        return view('totem/museum_menu', array_merge(
            $this->pageMeta(lang('Menu.museum')),
            [
                'nav' => $this->shellNav(base_url('menu')),
                'exploreLabel' => lang('Menu.museum'),
                'items' => [
                    $this->menuItem(lang('Menu.collection'), 'museo/coleccion', lang('Menu.collection_copy'), 'menu-card--museum', 'museo/coleccion/titeres/titere.webp'),
                    $this->menuItem(lang('Menu.el_museo'), 'museo/el-museo', lang('Menu.museum_copy'), 'menu-card--school', 'museo/el-museo/explora-el-museo.webp'),
                    $this->menuItem(lang('Menu.comic_history'), 'museo/historia', lang('Menu.comic_history_copy'), 'menu-card--history', 'museo/historia/historia-editorial.webp'),
                    $this->menuItem(lang('Menu.visits'), 'visitas-guiadas', lang('Menu.visits_copy'), 'menu-card--visits', 'menu/menu_visitas.webp'),
                ],
            ]
        ));
    }

    public function museumInfoMain(): string
    {
        return view('totem/museum_info_main', array_merge(
            $this->pageMeta(lang('Menu.el_museo')),
            ['nav' => $this->shellNav(base_url('museo'))]
        ));
    }

    public function museumBuilding(): string
    {
        return view('totem/museum_building', array_merge(
            $this->pageMeta(lang('MuseumInfo.teatromuseo_history_title')),
            [
                'nav' => $this->shellNav(base_url('museo/el-museo')),
                'data' => $this->totemApi()->museum(),
            ]
        ));
    }

    public function museumInstitution(): string
    {
        return view('totem/museum_institution', array_merge(
            $this->pageMeta(lang('MuseumInfo.church_history_title')),
            [
                'nav' => $this->shellNav(base_url('museo/el-museo')),
                'data' => $this->totemApi()->museum(),
            ]
        ));
    }

    public function museumToday(): string
    {
        $presenter = new MuseumTodayPresenter();

        return view('totem/museum_today', array_merge(
            $this->pageMeta(lang('MuseumInfo.teatromuseo_today')),
            [
                'nav' => $this->shellNav(base_url('museo/el-museo')),
                'today' => $presenter->present($this->totemApi()->museum()),
            ]
        ));
    }

    public function museumHistoryMain(): string
    {
        return view('totem/comic_history_main', array_merge(
            $this->pageMeta(lang('ComicHistory.main_title')),
            [
                'nav' => $this->shellNav(base_url('museo')),
            ]
        ));
    }

    public function museumComicHistoryMain(): string
    {
        return $this->museumHistoryMain();
    }

    public function museumHistoryPost(string $slug): string
    {
        return view('totem/comic_history_post', array_merge(
            $this->pageMeta(lang('ComicHistory.main_title')),
            [
                'nav' => $this->shellNav(base_url('museo/historia')),
                'post' => $this->totemApi()->museumHistory($slug),
                'slug' => $slug,
            ]
        ));
    }
}
