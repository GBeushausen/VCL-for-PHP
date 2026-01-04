<?php
/**
 * VCL for PHP 3.0 - Tailwind CSS 4 Demo
 *
 * This demo showcases the Tailwind CSS integration with
 * FlexPanel and GridPanel for responsive layouts.
 *
 * Features:
 * - All UI built using VCL components
 * - Theme-aware classes for light/dark mode
 * - Live preview with htmx AJAX updates
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use VCL\ExtCtrls\FlexPanel;
use VCL\ExtCtrls\GridPanel;
use VCL\ExtCtrls\Panel;
use VCL\StdCtrls\Button;
use VCL\StdCtrls\Edit;
use VCL\StdCtrls\Label;
use VCL\StdCtrls\Memo;
use VCL\UI\Enums\FlexDirection;
use VCL\UI\Enums\FlexWrap;
use VCL\UI\Enums\JustifyContent;
use VCL\UI\Enums\AlignItems;
use VCL\UI\Enums\RenderMode;
use VCL\Theming\ThemeManager;
use VCL\Security\Escaper;

$theme = ThemeManager::getInstance();

// Handle htmx AJAX requests for live preview
if (isset($_SERVER['HTTP_HX_REQUEST']) && $_SERVER['HTTP_HX_REQUEST'] === 'true') {
    header('Content-Type: text/html; charset=UTF-8');

    $name = trim($_POST['NameEdit'] ?? '');
    $email = trim($_POST['EmailEdit'] ?? '');
    $message = trim($_POST['MessageEdit'] ?? '');

    // Build the preview card
    ob_start();
    ?>
    <div class="space-y-4">
        <?php if ($name !== '' || $email !== '' || $message !== ''): ?>
            <!-- Contact Card Preview -->
            <div class="bg-vcl-surface-elevated rounded-lg shadow-vcl-md overflow-hidden">
                <!-- Header with avatar -->
                <div class="bg-vcl-primary p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-white text-xl font-bold">
                            <?= Escaper::html(mb_strtoupper(mb_substr($name ?: '?', 0, 1))) ?>
                        </div>
                        <div class="text-white">
                            <div class="font-semibold text-lg">
                                <?= $name !== '' ? Escaper::html($name) : '<span class="opacity-60">Name...</span>' ?>
                            </div>
                            <div class="text-white/80 text-sm">
                                <?= $email !== '' ? Escaper::html($email) : '<span class="opacity-60">email@example.com</span>' ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message content -->
                <div class="p-4">
                    <?php if ($message !== ''): ?>
                        <div class="text-vcl-text-muted text-sm mb-2">Message:</div>
                        <div class="text-vcl-text bg-vcl-surface-sunken rounded p-3 text-sm">
                            <?= nl2br(Escaper::html($message)) ?>
                        </div>
                    <?php else: ?>
                        <div class="text-vcl-text-muted text-sm italic">
                            Enter a message to see the preview...
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="bg-vcl-surface-elevated rounded p-2">
                    <div class="text-2xl font-bold text-vcl-primary"><?= mb_strlen($name) ?></div>
                    <div class="text-xs text-vcl-text-muted">Name chars</div>
                </div>
                <div class="bg-vcl-surface-elevated rounded p-2">
                    <div class="text-2xl font-bold text-vcl-primary"><?= mb_strlen($message) ?></div>
                    <div class="text-xs text-vcl-text-muted">Message chars</div>
                </div>
                <div class="bg-vcl-surface-elevated rounded p-2">
                    <div class="text-2xl font-bold text-vcl-primary"><?= str_contains($email, '@') ? '1' : '0' ?></div>
                    <div class="text-xs text-vcl-text-muted">Valid email</div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-vcl-text-muted text-center py-8">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <p>Start typing to see the live preview</p>
            </div>
        <?php endif; ?>
    </div>
    <?php
    echo ob_get_clean();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" <?= $theme->getThemeAttribute() ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCL Tailwind CSS 4 Demo</title>

    <!-- Include compiled Tailwind CSS (run npm run build first) -->
    <link rel="stylesheet" href="../public/assets/css/vcl-theme.css">
    <!-- htmx for AJAX -->
    <script src="https://unpkg.com/htmx.org@2.0.4"></script>
</head>
<body class="bg-vcl-surface-sunken min-h-screen p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-vcl-text mb-2">VCL Tailwind CSS 4 Demo</h1>
        <p class="text-vcl-text-muted mb-8">Interactive demo with live preview using htmx</p>

        <!-- Theme Switcher -->
        <div class="mb-8">
            <button onclick="VCLTheme?.toggle()" class="vcl-button">
                Toggle Dark Mode
            </button>
        </div>

        <!-- Section 1: Interactive Form with Live Preview -->
        <section class="mb-12">
            <h2 class="text-2xl font-semibold text-vcl-text mb-4">Interactive Contact Form</h2>
            <p class="text-sm text-vcl-text-muted mb-4">
                Type in the form fields - the preview updates in real-time via htmx AJAX.
            </p>

            <?php
            // Note: Grid wrapper is raw HTML because Tailwind doesn't detect dynamically
            // generated grid-cols-X classes from GridPanel's Columns property
            ?>
            <div id="MainGrid" class="grid grid-cols-2 gap-6 items-start">
            <?php
            // Left column: Contact Form
            $formPanel = new FlexPanel();
            $formPanel->Name = 'ContactForm';
            $formPanel->Direction = FlexDirection::Column;
            $formPanel->FlexGap = 'gap-4';
            $formPanel->Padding = 'p-6';
            $formPanel->Classes = ['bg-vcl-surface-elevated', 'rounded-lg', 'shadow-vcl-md'];

            // Form title
            $formTitle = new Label($formPanel);
            $formTitle->Name = 'FormTitle';
            $formTitle->Caption = 'Contact Information';
            $formTitle->RenderMode = RenderMode::Tailwind;
            $formTitle->Classes = ['font-semibold', 'text-lg', 'text-vcl-text', 'pb-2', 'border-b', 'border-vcl-border'];
            $formTitle->Parent = $formPanel;

            // Name field
            $nameWrapper = new FlexPanel($formPanel);
            $nameWrapper->Name = 'NameWrapper';
            $nameWrapper->Direction = FlexDirection::Column;
            $nameWrapper->FlexGap = 'gap-1';
            $nameWrapper->Parent = $formPanel;

            $nameLabel = new Label($nameWrapper);
            $nameLabel->Name = 'NameLabel';
            $nameLabel->Caption = 'Name';
            $nameLabel->RenderMode = RenderMode::Tailwind;
            $nameLabel->Parent = $nameWrapper;

            $nameEdit = new Edit($nameWrapper);
            $nameEdit->Name = 'NameEdit';
            $nameEdit->RenderMode = RenderMode::Tailwind;
            $nameEdit->Placeholder = 'Enter your name';
            // htmx: update preview on keyup with 300ms delay
            $nameEdit->ExtraAttributes = 'hx-post="" hx-trigger="keyup changed delay:300ms" hx-target="#preview" hx-include="#ContactForm input, #ContactForm textarea"';
            $nameEdit->Parent = $nameWrapper;

            // Email field
            $emailWrapper = new FlexPanel($formPanel);
            $emailWrapper->Name = 'EmailWrapper';
            $emailWrapper->Direction = FlexDirection::Column;
            $emailWrapper->FlexGap = 'gap-1';
            $emailWrapper->Parent = $formPanel;

            $emailLabel = new Label($emailWrapper);
            $emailLabel->Name = 'EmailLabel';
            $emailLabel->Caption = 'Email';
            $emailLabel->RenderMode = RenderMode::Tailwind;
            $emailLabel->Parent = $emailWrapper;

            $emailEdit = new Edit($emailWrapper);
            $emailEdit->Name = 'EmailEdit';
            $emailEdit->RenderMode = RenderMode::Tailwind;
            $emailEdit->Placeholder = 'you@example.com';
            $emailEdit->ExtraAttributes = 'hx-post="" hx-trigger="keyup changed delay:300ms" hx-target="#preview" hx-include="#ContactForm input, #ContactForm textarea"';
            $emailEdit->Parent = $emailWrapper;

            // Message field
            $messageWrapper = new FlexPanel($formPanel);
            $messageWrapper->Name = 'MessageWrapper';
            $messageWrapper->Direction = FlexDirection::Column;
            $messageWrapper->FlexGap = 'gap-1';
            $messageWrapper->Parent = $formPanel;

            $messageLabel = new Label($messageWrapper);
            $messageLabel->Name = 'MessageLabel';
            $messageLabel->Caption = 'Message';
            $messageLabel->RenderMode = RenderMode::Tailwind;
            $messageLabel->Parent = $messageWrapper;

            $messageMemo = new Memo($messageWrapper);
            $messageMemo->Name = 'MessageEdit';
            $messageMemo->RenderMode = RenderMode::Tailwind;
            $messageMemo->Rows = 4;
            $messageMemo->Placeholder = 'Write your message here...';
            $messageMemo->ExtraAttributes = 'hx-post="" hx-trigger="keyup changed delay:300ms" hx-target="#preview" hx-include="#ContactForm input, #ContactForm textarea"';
            $messageMemo->Parent = $messageWrapper;

            // Button row
            $buttonRow = new FlexPanel($formPanel);
            $buttonRow->Name = 'ButtonRow';
            $buttonRow->Direction = FlexDirection::Row;
            $buttonRow->Wrap = FlexWrap::Wrap;
            $buttonRow->FlexGap = 'gap-2';
            $buttonRow->Padding = 'pt-4';
            $buttonRow->Parent = $formPanel;

            $submitBtn = new Button($buttonRow);
            $submitBtn->Name = 'SubmitBtn';
            $submitBtn->Caption = 'Send Message';
            $submitBtn->RenderMode = RenderMode::Tailwind;
            $submitBtn->ThemeVariant = 'primary';
            $submitBtn->Parent = $buttonRow;

            $clearBtn = new Button($buttonRow);
            $clearBtn->Name = 'ClearBtn';
            $clearBtn->Caption = 'Clear';
            $clearBtn->ButtonType = 'btButton';
            $clearBtn->RenderMode = RenderMode::Tailwind;
            $clearBtn->ExtraAttributes = 'onclick="document.querySelectorAll(\'#ContactForm input, #ContactForm textarea\').forEach(e => e.value = \'\'); htmx.trigger(\'#NameEdit\', \'keyup\')"';
            $clearBtn->Parent = $buttonRow;

            // Render the form panel
            $formPanel->dumpContents();

            // Right column: Preview panel
            $previewPanel = new FlexPanel();
            $previewPanel->Name = 'PreviewPanel';
            $previewPanel->Direction = FlexDirection::Column;
            $previewPanel->FlexGap = 'gap-4';
            $previewPanel->Padding = 'p-6';
            $previewPanel->Classes = ['bg-vcl-surface-elevated', 'rounded-lg', 'shadow-vcl-md'];

            // Preview title
            $previewTitle = new Label($previewPanel);
            $previewTitle->Name = 'PreviewTitle';
            $previewTitle->Caption = 'Live Preview';
            $previewTitle->RenderMode = RenderMode::Tailwind;
            $previewTitle->Classes = ['font-semibold', 'text-lg', 'text-vcl-text', 'pb-2', 'border-b', 'border-vcl-border'];
            $previewTitle->Parent = $previewPanel;

            // Render the preview panel
            $previewPanel->dumpContents();
            ?>
            </div><!-- /MainGrid -->

            <!-- Preview content area (updated by htmx) -->
            <script>
                // Initialize preview after page load
                document.addEventListener('DOMContentLoaded', function() {
                    // Add the preview div inside PreviewPanel
                    const previewPanel = document.getElementById('PreviewPanel');
                    if (previewPanel) {
                        const previewDiv = document.createElement('div');
                        previewDiv.id = 'preview';
                        previewDiv.innerHTML = '<div class="text-vcl-text-muted text-center py-8"><svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg><p>Start typing to see the live preview</p></div>';
                        previewPanel.appendChild(previewDiv);
                    }
                });
            </script>
        </section>

        <!-- Section 2: GridPanel Card Layout -->
        <section class="mb-12">
            <h2 class="text-2xl font-semibold text-vcl-text mb-4">GridPanel - Responsive Card Grid</h2>
            <p class="text-sm text-vcl-text-muted mb-4">
                GridPanel automatically creates a responsive grid layout.
            </p>

            <?php
            // Create a GridPanel for cards
            $cardGrid = new GridPanel();
            $cardGrid->Name = 'CardGrid';
            $cardGrid->Columns = 1;
            $cardGrid->ResponsiveColumns = ['sm' => 2, 'lg' => 3];
            $cardGrid->GridGap = 'gap-6';

            // Create card panels
            $features = [
                ['title' => 'FlexPanel', 'desc' => 'Flexible box layout with direction, wrap, justify, and align controls.'],
                ['title' => 'GridPanel', 'desc' => 'CSS Grid layout with responsive column configuration.'],
                ['title' => 'Theme Support', 'desc' => 'Light and dark mode with CSS custom properties.'],
                ['title' => 'htmx Integration', 'desc' => 'Declarative AJAX for interactive components.'],
                ['title' => 'Tailwind CSS 4', 'desc' => 'Modern utility-first CSS with @theme directive.'],
                ['title' => 'PHP 8.4', 'desc' => 'Property hooks, enums, and modern PHP features.'],
            ];

            foreach ($features as $i => $feature) {
                $card = new Panel($cardGrid);
                $card->Name = "Card" . ($i + 1);
                $card->RenderMode = RenderMode::Tailwind;
                $card->Parent = $cardGrid;

                $cardTitle = new Label($card);
                $cardTitle->Name = "CardTitle" . ($i + 1);
                $cardTitle->Caption = $feature['title'];
                $cardTitle->RenderMode = RenderMode::Tailwind;
                $cardTitle->Classes = ['font-semibold', 'text-lg', 'mb-2'];
                $cardTitle->Parent = $card;

                $cardDesc = new Label($card);
                $cardDesc->Name = "CardDesc" . ($i + 1);
                $cardDesc->Caption = $feature['desc'];
                $cardDesc->RenderMode = RenderMode::Tailwind;
                $cardDesc->Classes = ['text-vcl-text-muted', 'text-sm'];
                $cardDesc->Parent = $card;
            }

            $cardGrid->dumpContents();
            ?>
        </section>

        <!-- Section 3: Horizontal FlexPanel (Navbar) -->
        <section class="mb-12">
            <h2 class="text-2xl font-semibold text-vcl-text mb-4">FlexPanel - Navigation Bar</h2>
            <p class="text-sm text-vcl-text-muted mb-4">
                FlexPanel with justify-between for logo, nav links, and action button.
            </p>

            <?php
            $navbar = new FlexPanel();
            $navbar->Name = 'NavBar';
            $navbar->Direction = FlexDirection::Row;
            $navbar->JustifyContent = JustifyContent::Between;
            $navbar->AlignItems = AlignItems::Center;
            $navbar->Wrap = FlexWrap::Wrap;
            $navbar->FlexGap = 'gap-4';
            $navbar->Padding = 'p-4';
            $navbar->Classes = ['bg-vcl-surface-elevated', 'rounded-lg', 'shadow-vcl-md'];

            $logo = new Label($navbar);
            $logo->Name = 'Logo';
            $logo->Caption = 'VCL Logo';
            $logo->RenderMode = RenderMode::Tailwind;
            $logo->ThemeVariant = '';
            $logo->Classes = ['font-bold', 'text-xl', 'text-vcl-primary'];
            $logo->Parent = $navbar;

            $navLinks = new FlexPanel($navbar);
            $navLinks->Name = 'NavLinks';
            $navLinks->Direction = FlexDirection::Row;
            $navLinks->Wrap = FlexWrap::Wrap;
            $navLinks->FlexGap = 'gap-4';
            $navLinks->Parent = $navbar;

            foreach (['Home', 'About', 'Services', 'Contact'] as $linkText) {
                $link = new Label($navLinks);
                $link->Name = 'Nav' . $linkText;
                $link->Caption = $linkText;
                $link->RenderMode = RenderMode::Tailwind;
                $link->ThemeVariant = '';
                $link->Classes = ['text-vcl-text-muted', 'hover:text-vcl-primary', 'cursor-pointer', 'transition-colors'];
                $link->Parent = $navLinks;
            }

            $signInBtn = new Button($navbar);
            $signInBtn->Name = 'SignInBtn';
            $signInBtn->Caption = 'Sign In';
            $signInBtn->ButtonType = 'btButton';
            $signInBtn->RenderMode = RenderMode::Tailwind;
            $signInBtn->ThemeVariant = 'primary';
            $signInBtn->Parent = $navbar;

            $navbar->dumpContents();
            ?>
        </section>

        <!-- Code Example -->
        <section class="mb-12">
            <h2 class="text-2xl font-semibold text-vcl-text mb-4">PHP Code Example</h2>

            <pre class="bg-gray-900 text-gray-100 p-6 rounded-lg overflow-x-auto text-sm"><code>&lt;?php
use VCL\ExtCtrls\FlexPanel;
use VCL\ExtCtrls\GridPanel;
use VCL\StdCtrls\Button;
use VCL\StdCtrls\Edit;
use VCL\StdCtrls\Label;
use VCL\StdCtrls\Memo;
use VCL\UI\Enums\FlexDirection;
use VCL\UI\Enums\RenderMode;

// Create a two-column grid layout
$grid = new GridPanel();
$grid-&gt;Name = 'MainGrid';
$grid-&gt;Columns = 2;
$grid-&gt;GridGap = 'gap-6';

// Left column: Form panel
$form = new FlexPanel($grid);
$form-&gt;Name = 'ContactForm';
$form-&gt;Direction = FlexDirection::Column;
$form-&gt;FlexGap = 'gap-4';
$form-&gt;Padding = 'p-6';
$form-&gt;Classes = ['bg-vcl-surface-elevated', 'rounded-lg', 'shadow-vcl-md'];
$form-&gt;Parent = $grid;

// Add a label
$label = new Label($form);
$label-&gt;Name = 'NameLabel';
$label-&gt;Caption = 'Name';
$label-&gt;RenderMode = RenderMode::Tailwind;
$label-&gt;Parent = $form;

// Add an input with htmx for live updates
$edit = new Edit($form);
$edit-&gt;Name = 'NameEdit';
$edit-&gt;RenderMode = RenderMode::Tailwind;
$edit-&gt;Placeholder = 'Enter your name';
$edit-&gt;ExtraAttributes = 'hx-post="" hx-trigger="keyup changed delay:300ms" hx-target="#preview"';
$edit-&gt;Parent = $form;

// Add a textarea (Memo)
$memo = new Memo($form);
$memo-&gt;Name = 'MessageEdit';
$memo-&gt;RenderMode = RenderMode::Tailwind;
$memo-&gt;Rows = 4;
$memo-&gt;Placeholder = 'Your message...';
$memo-&gt;Parent = $form;

// Add a button
$btn = new Button($form);
$btn-&gt;Name = 'SubmitBtn';
$btn-&gt;Caption = 'Submit';
$btn-&gt;RenderMode = RenderMode::Tailwind;
$btn-&gt;ThemeVariant = 'primary';
$btn-&gt;Parent = $form;

// Right column: Preview panel
$preview = new FlexPanel($grid);
$preview-&gt;Name = 'PreviewPanel';
$preview-&gt;Classes = ['bg-vcl-surface-elevated', 'rounded-lg', 'shadow-vcl-md', 'p-6'];
$preview-&gt;Parent = $grid;

// Render it
$grid-&gt;dumpContents();
?&gt;</code></pre>
        </section>
    </div>

    <?= $theme->getThemeSwitchScript() ?>
</body>
</html>
