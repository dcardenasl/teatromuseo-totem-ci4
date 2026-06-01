# Teatromuseo Totem - Project Standards

## View Standardization (UI/UX)
All views in `app/Views/totem/` must follow a consistent structure:

1.  **Layout:** All views must extend `layouts/MainLayout`.
2.  **Top Navigation:** Include `totem/partials/topbar` at the top of the content.
3.  **Page Title (Universal Component):**
    All pages must use the following structure for their titles to ensure visual consistency while keeping language keys clean. The title is rendered as a single string and may include `<br>` and `<strong>` when a screen needs a line break or emphasis inside the heading.

    ```html
    <section class="menu-title">
        <h1 class="menu-title__heading" id="page-title">
            <?php 
                $title = lang('YOUR_LANG_KEY');
                echo $title;
            ?>
        </h1>
    </section>
    ```
4.  **Content:** Wrap main content in a `<main class="page-content">` tag (or equivalent, ensuring class naming consistency across the app).
5.  **Language:** Use `lang()` for all user-facing strings. Do not hardcode text.
