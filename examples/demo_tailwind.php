<?php
/**
 * VCL for PHP 3.0 - Tailwind CSS 4 Demo
 *
 * This demo showcases the Tailwind CSS integration with
 * FlexPanel and GridPanel for responsive layouts.
 *
 * Features:
 * - All UI built using VCL components (including Page)
 * - Theme-aware classes for light/dark mode
 * - Live preview with htmx AJAX updates
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use VCL\Forms\Application;
use VCL\Forms\Page;
use VCL\ExtCtrls\FlexPanel;
use VCL\ExtCtrls\GridPanel;
use VCL\ExtCtrls\Panel;
use VCL\StdCtrls\Button;
use VCL\StdCtrls\Edit;
use VCL\StdCtrls\Html;
use VCL\StdCtrls\Label;
use VCL\StdCtrls\Memo;
use VCL\UI\Enums\FlexDirection;
use VCL\UI\Enums\FlexWrap;
use VCL\UI\Enums\JustifyContent;
use VCL\UI\Enums\AlignItems;
use VCL\UI\Enums\RenderMode;
use VCL\Security\Escaper;

/**
 * Tailwind Demo Page
 *
 * Demonstrates the VCL Page component with Tailwind CSS support.
 */
class TailwindDemoPage extends Page
{
    // Form components
    public ?FlexPanel $formPanel = null;
    public ?FlexPanel $previewPanel = null;
    public ?Edit $nameEdit = null;
    public ?Edit $emailEdit = null;
    public ?Memo $messageMemo = null;
    public ?Button $submitBtn = null;
    public ?Button $clearBtn = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        // Page configuration
        $this->Name = 'TailwindDemo';
        $this->Caption = 'VCL Tailwind CSS 4 Demo';
        $this->Language = 'en';

        // Enable Tailwind CSS
        $this->UseTailwind = true;
        $this->TailwindStylesheet = __DIR__ . '/../public/assets/css/vcl-theme.css';
        $this->BodyClasses = ['bg-vcl-surface-sunken', 'min-h-screen', 'p-8'];
        $this->DefaultTheme = 'light';

        // Enable htmx for AJAX
        $this->UseHtmx = true;

        // No form wrapping needed for this demo
        $this->IsForm = false;
    }

    /**
     * Handle htmx AJAX requests for live preview.
     */
    public function processHtmx(): void
    {
        if (!\VCL\Ajax\HtmxHandler::isHtmxRequest()) {
            return;
        }

        header('Content-Type: text/html; charset=UTF-8');

        $name = trim($_POST['NameEdit'] ?? '');
        $email = trim($_POST['EmailEdit'] ?? '');
        $message = trim($_POST['MessageEdit'] ?? '');

        // Build the preview using Html component with Twig template
        $preview = new Html();
        $preview->UseWrapper = false;
        $preview->TemplatePath = __DIR__ . '/templates';
        $preview->Template = 'contact-preview.twig';
        $preview->Variables = [
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'initial' => mb_strtoupper(mb_substr($name ?: '?', 0, 1)),
            'name_length' => mb_strlen($name),
            'message_length' => mb_strlen($message),
            'valid_email' => str_contains($email, '@'),
        ];
        $preview->show();
        exit;
    }

    /**
     * Create the page content.
     */
    protected function dumpChildren(): void
    {
        // Main container
        $mainContainer = new FlexPanel();
        $mainContainer->Name = 'MainContainer';
        $mainContainer->Direction = FlexDirection::Column;
        $mainContainer->Classes = ['max-w-6xl', 'mx-auto'];

        // Page header
        $pageTitle = new Html($mainContainer);
        $pageTitle->Name = 'PageTitle';
        $pageTitle->RenderMode = RenderMode::Tailwind;
        $pageTitle->WrapperTag = 'h1';
        $pageTitle->Classes = ['text-3xl', 'font-bold', 'text-vcl-text', 'mb-2'];
        $pageTitle->Html = 'VCL Tailwind CSS 4 Demo';
        $pageTitle->Parent = $mainContainer;

        $pageSubtitle = new Html($mainContainer);
        $pageSubtitle->Name = 'PageSubtitle';
        $pageSubtitle->RenderMode = RenderMode::Tailwind;
        $pageSubtitle->WrapperTag = 'p';
        $pageSubtitle->Classes = ['text-vcl-text-muted', 'mb-8'];
        $pageSubtitle->Html = 'Interactive demo with live preview using htmx - Built with VCL Page component';
        $pageSubtitle->Parent = $mainContainer;

        // Theme toggle button wrapper
        $themeBtnWrapper = new FlexPanel($mainContainer);
        $themeBtnWrapper->Name = 'ThemeBtnWrapper';
        $themeBtnWrapper->Classes = ['mb-8'];
        $themeBtnWrapper->Parent = $mainContainer;

        $themeBtn = new Button($themeBtnWrapper);
        $themeBtn->Name = 'ThemeToggle';
        $themeBtn->Caption = 'Toggle Dark Mode';
        $themeBtn->ButtonType = 'btButton';
        $themeBtn->RenderMode = RenderMode::Tailwind;
        $themeBtn->ExtraAttributes = 'onclick="VCLTheme?.toggle()"';
        $themeBtn->Parent = $themeBtnWrapper;

        // Render main container
        $mainContainer->show();

        // Section 1: Interactive Form
        $this->renderFormSection();

        // Section 2: GridPanel Card Layout
        $this->renderCardGrid();

        // Section 3: FlexPanel Navbar
        $this->renderNavbar();

        // Section 4: Code Example
        $this->renderCodeExample();
    }

    /**
     * Render the interactive form section.
     */
    protected function renderFormSection(): void
    {
        // Section wrapper
        $section = new Html();
        $section->Name = 'FormSection';
        $section->RenderMode = RenderMode::Tailwind;
        $section->WrapperTag = 'section';
        $section->Classes = ['mb-12', 'max-w-6xl', 'mx-auto'];

        // Open section
        echo $section->renderOpen();

        // Section title
        $sectionTitle = new Html();
        $sectionTitle->Name = 'FormSectionTitle';
        $sectionTitle->RenderMode = RenderMode::Tailwind;
        $sectionTitle->WrapperTag = 'h2';
        $sectionTitle->Classes = ['text-2xl', 'font-semibold', 'text-vcl-text', 'mb-4'];
        $sectionTitle->Html = 'Interactive Contact Form';
        $sectionTitle->show();

        $sectionDesc = new Html();
        $sectionDesc->Name = 'FormSectionDesc';
        $sectionDesc->RenderMode = RenderMode::Tailwind;
        $sectionDesc->WrapperTag = 'p';
        $sectionDesc->Classes = ['text-sm', 'text-vcl-text-muted', 'mb-4'];
        $sectionDesc->Html = 'Type in the form fields - the preview updates in real-time via htmx AJAX.';
        $sectionDesc->show();

        // Two-column grid
        $mainGrid = new GridPanel();
        $mainGrid->Name = 'MainGrid';
        $mainGrid->Columns = 2;
        $mainGrid->GridGap = 'gap-6';
        $mainGrid->AlignItems = AlignItems::Start;

        // Start the grid container
        echo $mainGrid->renderOpen();

        // Left column: Form
        $this->renderContactForm();

        // Right column: Preview
        $this->renderPreviewPanel();

        // Close the grid container
        echo $mainGrid->renderClose();

        // Close section
        echo $section->renderClose();
    }

    /**
     * Render the contact form panel.
     */
    protected function renderContactForm(): void
    {
        $this->formPanel = new FlexPanel();
        $this->formPanel->Name = 'ContactForm';
        $this->formPanel->Direction = FlexDirection::Column;
        $this->formPanel->FlexGap = 'gap-4';
        $this->formPanel->Padding = 'p-6';
        $this->formPanel->Classes = ['bg-vcl-surface-elevated', 'rounded-lg', 'shadow-vcl-md'];

        // Form title
        $formTitle = new Label($this->formPanel);
        $formTitle->Name = 'FormTitle';
        $formTitle->Caption = 'Contact Information';
        $formTitle->RenderMode = RenderMode::Tailwind;
        $formTitle->Classes = ['font-semibold', 'text-lg', 'text-vcl-text', 'pb-2', 'border-b', 'border-vcl-border'];
        $formTitle->Parent = $this->formPanel;

        // Name field
        $nameWrapper = new FlexPanel($this->formPanel);
        $nameWrapper->Name = 'NameWrapper';
        $nameWrapper->Direction = FlexDirection::Column;
        $nameWrapper->FlexGap = 'gap-1';
        $nameWrapper->Parent = $this->formPanel;

        $nameLabel = new Label($nameWrapper);
        $nameLabel->Name = 'NameLabel';
        $nameLabel->Caption = 'Name';
        $nameLabel->RenderMode = RenderMode::Tailwind;
        $nameLabel->Parent = $nameWrapper;

        $this->nameEdit = new Edit($nameWrapper);
        $this->nameEdit->Name = 'NameEdit';
        $this->nameEdit->RenderMode = RenderMode::Tailwind;
        $this->nameEdit->Placeholder = 'Enter your name';
        $this->nameEdit->ExtraAttributes = 'hx-post="" hx-trigger="keyup changed delay:300ms" hx-target="#preview" hx-include="#ContactForm input, #ContactForm textarea"';
        $this->nameEdit->Parent = $nameWrapper;

        // Email field
        $emailWrapper = new FlexPanel($this->formPanel);
        $emailWrapper->Name = 'EmailWrapper';
        $emailWrapper->Direction = FlexDirection::Column;
        $emailWrapper->FlexGap = 'gap-1';
        $emailWrapper->Parent = $this->formPanel;

        $emailLabel = new Label($emailWrapper);
        $emailLabel->Name = 'EmailLabel';
        $emailLabel->Caption = 'Email';
        $emailLabel->RenderMode = RenderMode::Tailwind;
        $emailLabel->Parent = $emailWrapper;

        $this->emailEdit = new Edit($emailWrapper);
        $this->emailEdit->Name = 'EmailEdit';
        $this->emailEdit->RenderMode = RenderMode::Tailwind;
        $this->emailEdit->Placeholder = 'you@example.com';
        $this->emailEdit->ExtraAttributes = 'hx-post="" hx-trigger="keyup changed delay:300ms" hx-target="#preview" hx-include="#ContactForm input, #ContactForm textarea"';
        $this->emailEdit->Parent = $emailWrapper;

        // Message field
        $messageWrapper = new FlexPanel($this->formPanel);
        $messageWrapper->Name = 'MessageWrapper';
        $messageWrapper->Direction = FlexDirection::Column;
        $messageWrapper->FlexGap = 'gap-1';
        $messageWrapper->Parent = $this->formPanel;

        $messageLabel = new Label($messageWrapper);
        $messageLabel->Name = 'MessageLabel';
        $messageLabel->Caption = 'Message';
        $messageLabel->RenderMode = RenderMode::Tailwind;
        $messageLabel->Parent = $messageWrapper;

        $this->messageMemo = new Memo($messageWrapper);
        $this->messageMemo->Name = 'MessageEdit';
        $this->messageMemo->RenderMode = RenderMode::Tailwind;
        $this->messageMemo->Rows = 4;
        $this->messageMemo->Placeholder = 'Write your message here...';
        $this->messageMemo->ExtraAttributes = 'hx-post="" hx-trigger="keyup changed delay:300ms" hx-target="#preview" hx-include="#ContactForm input, #ContactForm textarea"';
        $this->messageMemo->Parent = $messageWrapper;

        // Button row
        $buttonRow = new FlexPanel($this->formPanel);
        $buttonRow->Name = 'ButtonRow';
        $buttonRow->Direction = FlexDirection::Row;
        $buttonRow->Wrap = FlexWrap::Wrap;
        $buttonRow->FlexGap = 'gap-2';
        $buttonRow->Padding = 'pt-4';
        $buttonRow->Parent = $this->formPanel;

        $this->submitBtn = new Button($buttonRow);
        $this->submitBtn->Name = 'SubmitBtn';
        $this->submitBtn->Caption = 'Send Message';
        $this->submitBtn->RenderMode = RenderMode::Tailwind;
        $this->submitBtn->ThemeVariant = 'primary';
        $this->submitBtn->Parent = $buttonRow;

        $this->clearBtn = new Button($buttonRow);
        $this->clearBtn->Name = 'ClearBtn';
        $this->clearBtn->Caption = 'Clear';
        $this->clearBtn->ButtonType = 'btButton';
        $this->clearBtn->RenderMode = RenderMode::Tailwind;
        $this->clearBtn->ExtraAttributes = 'onclick="document.querySelectorAll(\'#ContactForm input, #ContactForm textarea\').forEach(e => e.value = \'\'); htmx.trigger(\'#NameEdit\', \'keyup\')"';
        $this->clearBtn->Parent = $buttonRow;

        $this->formPanel->show();
    }

    /**
     * Render the preview panel.
     */
    protected function renderPreviewPanel(): void
    {
        $this->previewPanel = new FlexPanel();
        $this->previewPanel->Name = 'PreviewPanel';
        $this->previewPanel->Direction = FlexDirection::Column;
        $this->previewPanel->FlexGap = 'gap-4';
        $this->previewPanel->Padding = 'p-6';
        $this->previewPanel->Classes = ['bg-vcl-surface-elevated', 'rounded-lg', 'shadow-vcl-md'];

        // Preview title
        $previewTitle = new Label($this->previewPanel);
        $previewTitle->Name = 'PreviewTitle';
        $previewTitle->Caption = 'Live Preview';
        $previewTitle->RenderMode = RenderMode::Tailwind;
        $previewTitle->Classes = ['font-semibold', 'text-lg', 'text-vcl-text', 'pb-2', 'border-b', 'border-vcl-border'];
        $previewTitle->Parent = $this->previewPanel;

        // Preview content (htmx target)
        $previewContent = new Html($this->previewPanel);
        $previewContent->Name = 'preview';
        $previewContent->RenderMode = RenderMode::Tailwind;
        $previewContent->Html = $this->getEmptyPreviewHtml();
        $previewContent->Parent = $this->previewPanel;

        $this->previewPanel->show();
    }

    /**
     * Get the empty state HTML for the preview.
     */
    protected function getEmptyPreviewHtml(): string
    {
        return '<div class="text-vcl-text-muted text-center py-8">'
            . '<svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">'
            . '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'
            . '</svg>'
            . '<p>Start typing to see the live preview</p>'
            . '</div>';
    }

    /**
     * Render the card grid section.
     */
    protected function renderCardGrid(): void
    {
        // Section wrapper
        $section = new Html();
        $section->Name = 'CardGridSection';
        $section->RenderMode = RenderMode::Tailwind;
        $section->WrapperTag = 'section';
        $section->Classes = ['mb-12', 'max-w-6xl', 'mx-auto'];

        // Open section
        echo $section->renderOpen();

        // Section title
        $sectionTitle = new Html();
        $sectionTitle->Name = 'CardGridSectionTitle';
        $sectionTitle->RenderMode = RenderMode::Tailwind;
        $sectionTitle->WrapperTag = 'h2';
        $sectionTitle->Classes = ['text-2xl', 'font-semibold', 'text-vcl-text', 'mb-4'];
        $sectionTitle->Html = 'GridPanel - Responsive Card Grid';
        $sectionTitle->show();

        $sectionDesc = new Html();
        $sectionDesc->Name = 'CardGridSectionDesc';
        $sectionDesc->RenderMode = RenderMode::Tailwind;
        $sectionDesc->WrapperTag = 'p';
        $sectionDesc->Classes = ['text-sm', 'text-vcl-text-muted', 'mb-4'];
        $sectionDesc->Html = 'GridPanel automatically creates a responsive grid layout.';
        $sectionDesc->show();

        $cardGrid = new GridPanel();
        $cardGrid->Name = 'CardGrid';
        $cardGrid->Columns = 1;
        $cardGrid->ResponsiveColumns = ['sm' => 2, 'lg' => 3];
        $cardGrid->GridGap = 'gap-6';

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

        $cardGrid->show();

        // Close section
        echo $section->renderClose();
    }

    /**
     * Render the navbar section.
     */
    protected function renderNavbar(): void
    {
        // Section wrapper
        $section = new Html();
        $section->Name = 'NavbarSection';
        $section->RenderMode = RenderMode::Tailwind;
        $section->WrapperTag = 'section';
        $section->Classes = ['mb-12', 'max-w-6xl', 'mx-auto'];

        // Open section
        echo $section->renderOpen();

        // Section title
        $sectionTitle = new Html();
        $sectionTitle->Name = 'NavbarSectionTitle';
        $sectionTitle->RenderMode = RenderMode::Tailwind;
        $sectionTitle->WrapperTag = 'h2';
        $sectionTitle->Classes = ['text-2xl', 'font-semibold', 'text-vcl-text', 'mb-4'];
        $sectionTitle->Html = 'FlexPanel - Navigation Bar';
        $sectionTitle->show();

        $sectionDesc = new Html();
        $sectionDesc->Name = 'NavbarSectionDesc';
        $sectionDesc->RenderMode = RenderMode::Tailwind;
        $sectionDesc->WrapperTag = 'p';
        $sectionDesc->Classes = ['text-sm', 'text-vcl-text-muted', 'mb-4'];
        $sectionDesc->Html = 'FlexPanel with justify-between for logo, nav links, and action button.';
        $sectionDesc->show();

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

        $navbar->show();

        // Close section
        echo $section->renderClose();
    }

    /**
     * Render the code example section.
     */
    protected function renderCodeExample(): void
    {
        // Section wrapper
        $section = new Html();
        $section->Name = 'CodeExampleSection';
        $section->RenderMode = RenderMode::Tailwind;
        $section->WrapperTag = 'section';
        $section->Classes = ['mb-12', 'max-w-6xl', 'mx-auto'];

        // Section title
        $sectionTitle = new Html();
        $sectionTitle->Name = 'CodeExampleTitle';
        $sectionTitle->RenderMode = RenderMode::Tailwind;
        $sectionTitle->WrapperTag = 'h2';
        $sectionTitle->Classes = ['text-2xl', 'font-semibold', 'text-vcl-text', 'mb-4'];
        $sectionTitle->Html = 'PHP Code Example';

        // Code block
        $codeBlock = new Html();
        $codeBlock->Name = 'CodeBlock';
        $codeBlock->RenderMode = RenderMode::Tailwind;
        $codeBlock->WrapperTag = 'pre';
        $codeBlock->Classes = ['bg-gray-900', 'text-gray-100', 'p-6', 'rounded-lg', 'overflow-x-auto', 'text-sm'];
        $codeBlock->Html = '<code>' . htmlspecialchars($this->getCodeExample()) . '</code>';

        // Build section content
        $section->Html = $sectionTitle->render() . $codeBlock->render();
        $section->show();
    }

    /**
     * Get the PHP code example string.
     */
    protected function getCodeExample(): string
    {
        return <<<'CODE'
<?php
use VCL\Forms\Page;
use VCL\ExtCtrls\FlexPanel;
use VCL\StdCtrls\Button;
use VCL\StdCtrls\Edit;
use VCL\StdCtrls\Label;
use VCL\UI\Enums\FlexDirection;
use VCL\UI\Enums\RenderMode;

// Create a Page with Tailwind support
class MyPage extends Page
{
    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->Name = 'MyPage';
        $this->Caption = 'My VCL Page';

        // Enable Tailwind CSS
        $this->UseTailwind = true;
        $this->TailwindStylesheet = '/assets/css/vcl-theme.css';
        $this->BodyClasses = ['bg-vcl-surface-sunken', 'min-h-screen', 'p-8'];

        // Enable htmx for AJAX
        $this->UseHtmx = true;
    }

    protected function dumpChildren(): void
    {
        // Create a flex panel form
        $form = new FlexPanel();
        $form->Name = 'ContactForm';
        $form->Direction = FlexDirection::Column;
        $form->FlexGap = 'gap-4';
        $form->Padding = 'p-6';
        $form->Classes = ['bg-vcl-surface-elevated', 'rounded-lg', 'shadow-vcl-md'];

        // Add a label
        $label = new Label($form);
        $label->Caption = 'Name';
        $label->RenderMode = RenderMode::Tailwind;
        $label->Parent = $form;

        // Add an input with htmx for live updates
        $edit = new Edit($form);
        $edit->Name = 'NameEdit';
        $edit->RenderMode = RenderMode::Tailwind;
        $edit->Placeholder = 'Enter your name';
        $edit->ExtraAttributes = 'hx-post="" hx-trigger="keyup changed delay:300ms" hx-target="#preview"';
        $edit->Parent = $form;

        // Add a primary button
        $btn = new Button($form);
        $btn->Caption = 'Submit';
        $btn->RenderMode = RenderMode::Tailwind;
        $btn->ThemeVariant = 'primary';
        $btn->Parent = $form;

        $form->show();
    }
}

// Instantiate and show the page
$app = Application::getInstance();
$page = new MyPage($app);
$page->show();
CODE;
    }
}

// Instantiate and show the page
$application = Application::getInstance();
$page = new TailwindDemoPage($application);
$page->show();
