<?php
/**
 * VCL for PHP 3.0 - Tailwind CSS 4 Demo
 *
 * This demo showcases the Tailwind CSS integration with
 * FlexPanel and GridPanel for responsive layouts.
 *
 * All UI is built using VCL components, not raw HTML.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use VCL\ExtCtrls\FlexPanel;
use VCL\ExtCtrls\GridPanel;
use VCL\ExtCtrls\Panel;
use VCL\StdCtrls\Button;
use VCL\StdCtrls\Edit;
use VCL\StdCtrls\Label;
use VCL\UI\Enums\FlexDirection;
use VCL\UI\Enums\FlexWrap;
use VCL\UI\Enums\JustifyContent;
use VCL\UI\Enums\AlignItems;
use VCL\UI\Enums\RenderMode;
use VCL\Theming\ThemeManager;

$theme = ThemeManager::getInstance();
?>
<!DOCTYPE html>
<html lang="en" <?= $theme->getThemeAttribute() ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCL Tailwind CSS 4 Demo</title>

    <!-- Include compiled Tailwind CSS (run npm run build first) -->
    <link rel="stylesheet" href="../public/assets/css/vcl-theme.css">
</head>
<body class="bg-vcl-surface-sunken min-h-screen p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-vcl-text mb-8">VCL Tailwind CSS 4 Demo</h1>

        <!-- Theme Switcher -->
        <div class="mb-8">
            <button onclick="VCLTheme?.toggle()" class="vcl-button">
                Toggle Dark Mode
            </button>
        </div>

        <!-- Section 1: FlexPanel Form Layout -->
        <section class="mb-12">
            <h2 class="text-2xl font-semibold text-vcl-text mb-4">FlexPanel - Contact Form</h2>
            <p class="text-sm text-vcl-text-muted mb-4">
                All elements below are VCL components (FlexPanel, Label, Edit, Button) rendered with theme-aware classes.
            </p>

            <?php
            // Create a FlexPanel for the form
            // Using theme-aware classes: bg-vcl-surface-elevated instead of bg-white
            $formPanel = new FlexPanel();
            $formPanel->Name = 'ContactForm';
            $formPanel->Direction = FlexDirection::Column;
            $formPanel->FlexGap = 'gap-4';
            $formPanel->Padding = 'p-6';
            $formPanel->Classes = ['max-w-md', 'bg-vcl-surface-elevated', 'rounded-lg', 'shadow-vcl-md'];

            // Name field wrapper
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
            $nameEdit->Parent = $nameWrapper;

            // Email field wrapper
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
            $emailEdit->Parent = $emailWrapper;

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
            $submitBtn->Caption = 'Submit';
            $submitBtn->RenderMode = RenderMode::Tailwind;
            $submitBtn->ThemeVariant = 'primary';
            $submitBtn->Parent = $buttonRow;

            $cancelBtn = new Button($buttonRow);
            $cancelBtn->Name = 'CancelBtn';
            $cancelBtn->Caption = 'Cancel';
            $cancelBtn->ButtonType = 'btButton';
            $cancelBtn->RenderMode = RenderMode::Tailwind;
            $cancelBtn->Parent = $buttonRow;

            // Render the form
            $formPanel->dumpContents();
            ?>
        </section>

        <!-- Section 2: GridPanel Card Layout -->
        <section class="mb-12">
            <h2 class="text-2xl font-semibold text-vcl-text mb-4">GridPanel - Responsive Card Grid</h2>
            <p class="text-sm text-vcl-text-muted mb-4">
                GridPanel automatically creates a responsive grid: 1 column on mobile, 2 on sm, 3 on lg.
            </p>

            <?php
            // Create a GridPanel for cards
            $cardGrid = new GridPanel();
            $cardGrid->Name = 'CardGrid';
            $cardGrid->Columns = 1;
            $cardGrid->ResponsiveColumns = ['sm' => 2, 'lg' => 3];
            $cardGrid->GridGap = 'gap-6';

            // Create card panels
            for ($i = 1; $i <= 6; $i++) {
                $card = new Panel($cardGrid);
                $card->Name = "Card{$i}";
                $card->RenderMode = RenderMode::Tailwind;
                // vcl-panel is added automatically and uses theme colors
                $card->Parent = $cardGrid;

                // Card title
                $cardTitle = new Label($card);
                $cardTitle->Name = "CardTitle{$i}";
                $cardTitle->Caption = "Card {$i}";
                $cardTitle->RenderMode = RenderMode::Tailwind;
                $cardTitle->Classes = ['font-semibold', 'text-lg', 'mb-2'];
                $cardTitle->Parent = $card;

                // Card description - using theme-aware text color
                $cardDesc = new Label($card);
                $cardDesc->Name = "CardDesc{$i}";
                $cardDesc->Caption = 'This is a VCL Panel rendered with theme-aware CSS classes.';
                $cardDesc->RenderMode = RenderMode::Tailwind;
                $cardDesc->Classes = ['text-vcl-text-muted', 'text-sm', 'mb-4'];
                $cardDesc->Parent = $card;

                // Card button
                $cardBtn = new Button($card);
                $cardBtn->Name = "CardBtn{$i}";
                $cardBtn->Caption = 'Learn More';
                $cardBtn->ButtonType = 'btButton';
                $cardBtn->RenderMode = RenderMode::Tailwind;
                $cardBtn->ThemeVariant = 'primary';
                $cardBtn->Classes = ['text-sm'];
                $cardBtn->Parent = $card;
            }

            // Render the grid
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
            $navbar->FlexGap = 'gap-4';
            $navbar->Padding = 'p-4';
            // Using theme-aware classes
            $navbar->Classes = ['bg-vcl-surface-elevated', 'rounded-lg', 'shadow-vcl-md', 'flex-wrap'];

            // Logo - using theme-aware primary color
            $logo = new Label($navbar);
            $logo->Name = 'Logo';
            $logo->Caption = 'VCL Logo';
            $logo->RenderMode = RenderMode::Tailwind;
            $logo->ThemeVariant = '';
            $logo->Classes = ['font-bold', 'text-xl', 'text-vcl-primary'];
            $logo->Parent = $navbar;

            // Nav links wrapper
            $navLinks = new FlexPanel($navbar);
            $navLinks->Name = 'NavLinks';
            $navLinks->Direction = FlexDirection::Row;
            $navLinks->FlexGap = 'gap-4';
            $navLinks->Classes = ['flex-wrap'];
            $navLinks->Parent = $navbar;

            foreach (['Home', 'About', 'Services', 'Contact'] as $linkText) {
                $link = new Label($navLinks);
                $link->Name = 'Nav' . $linkText;
                $link->Caption = $linkText;
                $link->RenderMode = RenderMode::Tailwind;
                $link->ThemeVariant = '';
                // Using theme-aware text color
                $link->Classes = ['text-vcl-text-muted', 'hover:text-vcl-primary', 'cursor-pointer'];
                $link->Parent = $navLinks;
            }

            // Sign in button
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

            <pre class="bg-vcl-surface text-vcl-text p-6 rounded-lg overflow-x-auto text-sm border border-vcl-border"><code>&lt;?php
use VCL\ExtCtrls\FlexPanel;
use VCL\StdCtrls\Button;
use VCL\StdCtrls\Edit;
use VCL\StdCtrls\Label;
use VCL\UI\Enums\FlexDirection;
use VCL\UI\Enums\RenderMode;

// Create a flex container with theme-aware classes
$form = new FlexPanel();
$form->Name = 'ContactForm';
$form->Direction = FlexDirection::Column;
$form->FlexGap = 'gap-4';
// Use bg-vcl-surface-elevated instead of bg-white for dark mode support
$form->Classes = ['max-w-md', 'p-6', 'bg-vcl-surface-elevated', 'rounded-lg'];

// Add a label - vcl-label class is added automatically
$label = new Label($form);
$label->Name = 'NameLabel';
$label->Caption = 'Name';
$label->RenderMode = RenderMode::Tailwind;
$label->Parent = $form;

// Add an input - vcl-input class is added automatically
$edit = new Edit($form);
$edit->Name = 'NameEdit';
$edit->RenderMode = RenderMode::Tailwind;
$edit->Parent = $form;

// Add a button - ThemeVariant 'primary' adds vcl-button-primary
$btn = new Button($form);
$btn->Name = 'SubmitBtn';
$btn->Caption = 'Submit';
$btn->RenderMode = RenderMode::Tailwind;
$btn->ThemeVariant = 'primary';
$btn->Parent = $form;

// Render it
$form->dumpContents();
?&gt;</code></pre>
        </section>
    </div>

    <?= $theme->getThemeSwitchScript() ?>
</body>
</html>
