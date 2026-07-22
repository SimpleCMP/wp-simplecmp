<?php

namespace SimpleCMP\WpSimpleCmp\Admin;

class Menu
{
    public static function register(): void
    {
        add_menu_page(
            __('SimpleCMP', 'wp-simplecmp'),
            __('SimpleCMP', 'wp-simplecmp'),
            'manage_options',
            'simplecmp',
            [Pages\DetectionsPage::class, 'render'],
            'dashicons-lock',
            30
        );

        add_submenu_page(
            'simplecmp',
            __('Detections', 'wp-simplecmp'),
            __('Detections', 'wp-simplecmp'),
            'manage_options',
            'simplecmp',
            [Pages\DetectionsPage::class, 'render']
        );

        add_submenu_page(
            'simplecmp',
            __('Services', 'wp-simplecmp'),
            __('Services', 'wp-simplecmp'),
            'manage_options',
            'simplecmp-services',
            [Pages\ServicesPage::class, 'render']
        );

        add_submenu_page(
            'simplecmp',
            __('Library', 'wp-simplecmp'),
            __('Library', 'wp-simplecmp'),
            'manage_options',
            'simplecmp-library',
            [Pages\LibraryPage::class, 'render']
        );

        add_submenu_page(
            'simplecmp',
            __('Theme Designer', 'wp-simplecmp'),
            __('Theme Designer', 'wp-simplecmp'),
            'manage_options',
            'simplecmp-theme',
            [Pages\ThemeDesignerPage::class, 'render']
        );

        add_submenu_page(
            'simplecmp',
            __('Tracker Setup', 'wp-simplecmp'),
            __('Tracker Setup', 'wp-simplecmp'),
            'manage_options',
            'simplecmp-trackers',
            [Pages\TrackerSetupPage::class, 'render']
        );

        add_submenu_page(
            'simplecmp',
            __('Audit Trail', 'wp-simplecmp'),
            __('Audit Trail', 'wp-simplecmp'),
            'manage_options',
            'simplecmp-audit',
            [Pages\AuditPage::class, 'render']
        );

        add_submenu_page(
            'simplecmp',
            __('DSGVO Auskunft', 'wp-simplecmp'),
            __('DSGVO Auskunft', 'wp-simplecmp'),
            'manage_options',
            'simplecmp-auskunft',
            [Pages\AuskunftPage::class, 'render']
        );

        add_submenu_page(
            'simplecmp',
            __('Settings', 'wp-simplecmp'),
            __('Settings', 'wp-simplecmp'),
            'manage_options',
            'simplecmp-settings',
            [Pages\SettingsPage::class, 'render']
        );
    }
}
