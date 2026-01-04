<?php
/**
 * VCL for PHP - Erweiterte UI-Demo mit Tailwind CSS
 *
 * Demonstriert verschiedene UI-Komponenten mit modernem Tailwind-Layout.
 * Aufruf: http://vcl.ddev.site/demo_advanced.php
 */

declare(strict_types=1);

require_once(__DIR__ . '/../vendor/autoload.php');

use VCL\Forms\Page;
use VCL\Forms\Application;
use VCL\ExtCtrls\FlexPanel;
use VCL\StdCtrls\Label;
use VCL\StdCtrls\Edit;
use VCL\StdCtrls\Button;
use VCL\StdCtrls\CheckBox;
use VCL\StdCtrls\RadioButton;
use VCL\StdCtrls\ComboBox;
use VCL\StdCtrls\Memo;
use VCL\StdCtrls\Html;
use VCL\UI\Enums\FlexDirection;
use VCL\UI\Enums\RenderMode;

class AdvancedDemoPage extends Page
{
    // Form components - must be created in constructor for lifecycle
    public ?Edit $NameEdit = null;
    public ?Edit $EmailEdit = null;
    public ?ComboBox $CountryCombo = null;
    public ?CheckBox $NewsletterCheck = null;
    public ?Memo $CommentMemo = null;
    public ?RadioButton $GenderMale = null;
    public ?RadioButton $GenderFemale = null;
    public ?RadioButton $GenderOther = null;
    public ?Button $SubmitButton = null;
    public ?Label $ResultLabel = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->Name = "AdvancedDemoPage";
        $this->Caption = "VCL Advanced UI Demo";

        // Enable Tailwind CSS
        $this->UseTailwind = true;
        $this->TailwindStylesheet = __DIR__ . '/../public/assets/css/vcl-theme.css';
        $this->BodyClasses = ['bg-vcl-surface-sunken', 'min-h-screen', 'p-8'];

        // Create all form components in constructor so they exist during lifecycle
        $this->GenderMale = new RadioButton($this);
        $this->GenderMale->Name = 'GenderMale';
        $this->GenderMale->Caption = 'Herr';
        $this->GenderMale->Group = 'gender';
        $this->GenderMale->RenderMode = RenderMode::Tailwind;

        $this->GenderFemale = new RadioButton($this);
        $this->GenderFemale->Name = 'GenderFemale';
        $this->GenderFemale->Caption = 'Frau';
        $this->GenderFemale->Group = 'gender';
        $this->GenderFemale->RenderMode = RenderMode::Tailwind;

        $this->GenderOther = new RadioButton($this);
        $this->GenderOther->Name = 'GenderOther';
        $this->GenderOther->Caption = 'Divers';
        $this->GenderOther->Group = 'gender';
        $this->GenderOther->RenderMode = RenderMode::Tailwind;

        $this->NameEdit = new Edit($this);
        $this->NameEdit->Name = 'NameEdit';
        $this->NameEdit->RenderMode = RenderMode::Tailwind;
        $this->NameEdit->Classes = ['flex-1'];
        $this->NameEdit->Required = true;
        $this->NameEdit->MinLength = 2;
        $this->NameEdit->Placeholder = 'Ihr vollstaendiger Name';

        $this->EmailEdit = new Edit($this);
        $this->EmailEdit->Name = 'EmailEdit';
        $this->EmailEdit->RenderMode = RenderMode::Tailwind;
        $this->EmailEdit->Classes = ['flex-1'];
        $this->EmailEdit->Required = true;
        $this->EmailEdit->InputType = 'email';
        $this->EmailEdit->Placeholder = 'ihre@email.de';

        $this->CountryCombo = new ComboBox($this);
        $this->CountryCombo->Name = 'CountryCombo';
        $this->CountryCombo->RenderMode = RenderMode::Tailwind;
        $this->CountryCombo->Classes = ['flex-1'];
        $this->CountryCombo->Items = [
            'Deutschland',
            'Oesterreich',
            'Schweiz',
            'Liechtenstein',
            'Luxemburg'
        ];

        $this->NewsletterCheck = new CheckBox($this);
        $this->NewsletterCheck->Name = 'NewsletterCheck';
        $this->NewsletterCheck->Caption = 'Ja, ich moechte den Newsletter erhalten';
        $this->NewsletterCheck->RenderMode = RenderMode::Tailwind;

        $this->CommentMemo = new Memo($this);
        $this->CommentMemo->Name = 'CommentMemo';
        $this->CommentMemo->RenderMode = RenderMode::Tailwind;
        $this->CommentMemo->Rows = 4;
        $this->CommentMemo->Classes = ['flex-1'];
        $this->CommentMemo->Placeholder = 'Ihr Kommentar (optional)';

        $this->SubmitButton = new Button($this);
        $this->SubmitButton->Name = 'SubmitButton';
        $this->SubmitButton->Caption = 'Absenden';
        $this->SubmitButton->RenderMode = RenderMode::Tailwind;
        $this->SubmitButton->ThemeVariant = 'primary';
        $this->SubmitButton->OnClick = 'SubmitButtonClick';

        $this->ResultLabel = new Label($this);
        $this->ResultLabel->Name = 'ResultLabel';
        $this->ResultLabel->Caption = '';
        $this->ResultLabel->RenderMode = RenderMode::Tailwind;
        $this->ResultLabel->HtmlContent = true;
        $this->ResultLabel->Classes = ['p-4', 'bg-green-50', 'text-green-800', 'rounded-lg', 'hidden'];
    }

    protected function dumpChildren(): void
    {
        // Main container
        $main = new FlexPanel();
        $main->Name = 'MainContainer';
        $main->Direction = FlexDirection::Column;
        $main->FlexGap = 'gap-6';
        $main->Classes = ['max-w-4xl', 'mx-auto'];

        // Title
        $title = new Html($main);
        $title->Name = 'Title';
        $title->WrapperTag = 'h1';
        $title->RenderMode = RenderMode::Tailwind;
        $title->Classes = ['text-3xl', 'font-bold', 'text-vcl-text'];
        $title->Html = 'VCL for PHP - UI Komponenten Demo';
        $title->Parent = $main;

        // Form Panel
        $formPanel = new FlexPanel($main);
        $formPanel->Name = 'FormPanel';
        $formPanel->Direction = FlexDirection::Column;
        $formPanel->FlexGap = 'gap-4';
        $formPanel->Padding = 'p-6';
        $formPanel->Classes = ['bg-vcl-surface-elevated', 'rounded-lg', 'shadow-vcl-md'];
        $formPanel->Parent = $main;

        // Gender (Radio Buttons)
        $genderRow = new FlexPanel($formPanel);
        $genderRow->Name = 'GenderRow';
        $genderRow->Direction = FlexDirection::Row;
        $genderRow->FlexGap = 'gap-4';
        $genderRow->Classes = ['items-center'];
        $genderRow->Parent = $formPanel;

        $genderLabel = new Label($genderRow);
        $genderLabel->Name = 'GenderLabel';
        $genderLabel->Caption = 'Anrede:';
        $genderLabel->RenderMode = RenderMode::Tailwind;
        $genderLabel->Classes = ['w-24', 'font-medium'];
        $genderLabel->Parent = $genderRow;

        // Attach existing radio buttons to layout
        $this->GenderMale->Parent = $genderRow;
        $this->GenderFemale->Parent = $genderRow;
        $this->GenderOther->Parent = $genderRow;

        // Name field
        $this->createFormRow($formPanel, 'Name:', $this->NameEdit);

        // Email field
        $this->createFormRow($formPanel, 'E-Mail:', $this->EmailEdit);

        // Country ComboBox
        $this->createFormRow($formPanel, 'Land:', $this->CountryCombo);

        // Newsletter Checkbox
        $checkRow = new FlexPanel($formPanel);
        $checkRow->Name = 'CheckRow';
        $checkRow->Direction = FlexDirection::Row;
        $checkRow->Classes = ['ml-0', 'sm:ml-28'];
        $checkRow->Parent = $formPanel;
        $this->NewsletterCheck->Parent = $checkRow;

        // Comment Memo
        $this->createFormRow($formPanel, 'Kommentar:', $this->CommentMemo);

        // Button row
        $buttonRow = new FlexPanel($formPanel);
        $buttonRow->Name = 'ButtonRow';
        $buttonRow->Direction = FlexDirection::Row;
        $buttonRow->FlexGap = 'gap-3';
        $buttonRow->Classes = ['ml-0', 'sm:ml-28', 'pt-2', 'flex-wrap'];
        $buttonRow->Parent = $formPanel;

        $this->SubmitButton->Parent = $buttonRow;

        $resetButton = new Button($buttonRow);
        $resetButton->Name = 'ResetButton';
        $resetButton->Caption = 'Zuruecksetzen';
        $resetButton->ButtonType = 'btButton';
        $resetButton->RenderMode = RenderMode::Tailwind;
        $resetButton->jsOnClick = 'VCL.clearForm(this.form)';
        $resetButton->Parent = $buttonRow;

        // Result Label
        $this->ResultLabel->Parent = $main;

        $main->show();
    }

    /**
     * Helper to create a form row with label and input.
     */
    protected function createFormRow(FlexPanel $parent, string $label, object $control): void
    {
        $row = new FlexPanel($parent);
        $row->Name = $control->Name . 'Row';
        $row->Direction = FlexDirection::Row;
        $row->FlexGap = 'gap-4';
        $row->Classes = ['items-start'];
        $row->Parent = $parent;

        $labelCtrl = new Label($row);
        $labelCtrl->Name = $control->Name . 'Label';
        $labelCtrl->Caption = $label;
        $labelCtrl->RenderMode = RenderMode::Tailwind;
        $labelCtrl->Classes = ['w-24', 'font-medium', 'pt-2'];
        $labelCtrl->Parent = $row;

        $control->Parent = $row;
    }

    public function SubmitButtonClick(object $sender, array $params): void
    {
        $output = '<strong>Formulardaten empfangen:</strong><br/>';

        // Gender
        $gender = 'Nicht angegeben';
        if ($this->GenderMale->Checked) {
            $gender = 'Herr';
        } elseif ($this->GenderFemale->Checked) {
            $gender = 'Frau';
        } elseif ($this->GenderOther->Checked) {
            $gender = 'Divers';
        }
        $output .= 'Anrede: ' . htmlspecialchars($gender) . '<br/>';

        // Name
        $output .= 'Name: ' . htmlspecialchars($this->NameEdit->Text) . '<br/>';

        // Email
        $output .= 'E-Mail: ' . htmlspecialchars($this->EmailEdit->Text) . '<br/>';

        // Country
        $country = $this->CountryCombo->ItemIndex >= 0
            ? $this->CountryCombo->Items[$this->CountryCombo->ItemIndex]
            : 'Nicht ausgewaehlt';
        $output .= 'Land: ' . htmlspecialchars($country) . '<br/>';

        // Newsletter
        $output .= 'Newsletter: ' . ($this->NewsletterCheck->Checked ? 'Ja' : 'Nein') . '<br/>';

        // Comment
        if (!empty($this->CommentMemo->Text)) {
            $output .= 'Kommentar: ' . htmlspecialchars($this->CommentMemo->Text) . '<br/>';
        }

        $this->ResultLabel->Caption = $output;
        $this->ResultLabel->Classes = ['p-4', 'bg-green-50', 'text-green-800', 'rounded-lg'];
    }
}

// Seite erstellen und anzeigen
$application = Application::getInstance();
$page = new AdvancedDemoPage($application);
$page->show();
