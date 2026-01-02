<?php
/**
 * VCL for PHP - Erweiterte UI-Demo
 *
 * Diese Datei demonstriert verschiedene UI-Komponenten des VCL-Frameworks.
 * Aufruf: http://vcl.ddev.site/demo_advanced.php
 */

declare(strict_types=1);

// Composer Autoloader einbinden
require_once(__DIR__ . '/vendor/autoload.php');

// Namespaces importieren
use VCL\Forms\Page;
use VCL\Forms\Application;
use VCL\StdCtrls\Label;
use VCL\StdCtrls\Edit;
use VCL\StdCtrls\Button;
use VCL\StdCtrls\CheckBox;
use VCL\StdCtrls\RadioButton;
use VCL\StdCtrls\ComboBox;
use VCL\StdCtrls\Memo;

// Erweiterte Demo-Page
class AdvancedDemoPage extends Page
{
    // UI-Komponenten
    public ?Label $TitleLabel = null;

    // Formular-Bereich
    public ?Label $NameLabel = null;
    public ?Edit $NameEdit = null;
    public ?Label $EmailLabel = null;
    public ?Edit $EmailEdit = null;
    public ?Label $CountryLabel = null;
    public ?ComboBox $CountryCombo = null;
    public ?CheckBox $NewsletterCheck = null;
    public ?Label $CommentLabel = null;
    public ?Memo $CommentMemo = null;

    // Buttons
    public ?Button $SubmitButton = null;
    public ?Button $ResetButton = null;

    // Ausgabe
    public ?Label $ResultLabel = null;

    // RadioGroup für Anrede
    public ?Label $GenderLabel = null;
    public ?RadioButton $GenderMale = null;
    public ?RadioButton $GenderFemale = null;
    public ?RadioButton $GenderOther = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->Name = "AdvancedDemoPage";
        $this->Caption = "VCL Advanced UI Demo";
        $this->Color = "#ffffff";

        $this->createTitleSection();
        $this->createFormSection();
        $this->createButtonSection();
        $this->createOutputSection();
    }

    public function createTitleSection(): void
    {
        // Haupt-Titel
        $this->TitleLabel = new Label($this);
        $this->TitleLabel->Name = "TitleLabel";
        $this->TitleLabel->Parent = $this;
        $this->TitleLabel->Left = 20;
        $this->TitleLabel->Top = 20;
        $this->TitleLabel->Caption = "VCL for PHP - UI Komponenten Demo";
        $this->TitleLabel->Font->Size = "24px";
        $this->TitleLabel->Font->Weight = "bold";
        $this->TitleLabel->Font->Color = "#333333";
    }

    public function createFormSection(): void
    {
        $baseTop = 70;
        $labelWidth = 120;
        $inputLeft = 150;
        $rowHeight = 35;

        // Anrede (RadioButtons)
        $this->GenderLabel = new Label($this);
        $this->GenderLabel->Name = "GenderLabel";
        $this->GenderLabel->Parent = $this;
        $this->GenderLabel->Left = 20;
        $this->GenderLabel->Top = $baseTop;
        $this->GenderLabel->Caption = "Anrede:";

        $this->GenderMale = new RadioButton($this);
        $this->GenderMale->Name = "GenderMale";
        $this->GenderMale->Parent = $this;
        $this->GenderMale->Left = $inputLeft;
        $this->GenderMale->Top = $baseTop;
        $this->GenderMale->Caption = "Herr";
        $this->GenderMale->Group = "gender";

        $this->GenderFemale = new RadioButton($this);
        $this->GenderFemale->Name = "GenderFemale";
        $this->GenderFemale->Parent = $this;
        $this->GenderFemale->Left = $inputLeft + 80;
        $this->GenderFemale->Top = $baseTop;
        $this->GenderFemale->Caption = "Frau";
        $this->GenderFemale->Group = "gender";

        $this->GenderOther = new RadioButton($this);
        $this->GenderOther->Name = "GenderOther";
        $this->GenderOther->Parent = $this;
        $this->GenderOther->Left = $inputLeft + 160;
        $this->GenderOther->Top = $baseTop;
        $this->GenderOther->Caption = "Divers";
        $this->GenderOther->Group = "gender";

        // Name
        $this->NameLabel = new Label($this);
        $this->NameLabel->Name = "NameLabel";
        $this->NameLabel->Parent = $this;
        $this->NameLabel->Left = 20;
        $this->NameLabel->Top = $baseTop + $rowHeight;
        $this->NameLabel->Caption = "Name:";

        $this->NameEdit = new Edit($this);
        $this->NameEdit->Name = "NameEdit";
        $this->NameEdit->Parent = $this;
        $this->NameEdit->Left = $inputLeft;
        $this->NameEdit->Top = $baseTop + $rowHeight - 3;
        $this->NameEdit->Width = 250;

        // Email
        $this->EmailLabel = new Label($this);
        $this->EmailLabel->Name = "EmailLabel";
        $this->EmailLabel->Parent = $this;
        $this->EmailLabel->Left = 20;
        $this->EmailLabel->Top = $baseTop + $rowHeight * 2;
        $this->EmailLabel->Caption = "E-Mail:";

        $this->EmailEdit = new Edit($this);
        $this->EmailEdit->Name = "EmailEdit";
        $this->EmailEdit->Parent = $this;
        $this->EmailEdit->Left = $inputLeft;
        $this->EmailEdit->Top = $baseTop + $rowHeight * 2 - 3;
        $this->EmailEdit->Width = 250;

        // Land (ComboBox)
        $this->CountryLabel = new Label($this);
        $this->CountryLabel->Name = "CountryLabel";
        $this->CountryLabel->Parent = $this;
        $this->CountryLabel->Left = 20;
        $this->CountryLabel->Top = $baseTop + $rowHeight * 3;
        $this->CountryLabel->Caption = "Land:";

        $this->CountryCombo = new ComboBox($this);
        $this->CountryCombo->Name = "CountryCombo";
        $this->CountryCombo->Parent = $this;
        $this->CountryCombo->Left = $inputLeft;
        $this->CountryCombo->Top = $baseTop + $rowHeight * 3 - 3;
        $this->CountryCombo->Width = 250;
        $this->CountryCombo->Items = [
            "Deutschland",
            "Oesterreich",
            "Schweiz",
            "Liechtenstein",
            "Luxemburg"
        ];

        // Newsletter Checkbox
        $this->NewsletterCheck = new CheckBox($this);
        $this->NewsletterCheck->Name = "NewsletterCheck";
        $this->NewsletterCheck->Parent = $this;
        $this->NewsletterCheck->Left = $inputLeft;
        $this->NewsletterCheck->Top = $baseTop + $rowHeight * 4;
        $this->NewsletterCheck->Caption = "Ja, ich moechte den Newsletter erhalten";
        $this->NewsletterCheck->Width = 300;

        // Kommentar (Memo)
        $this->CommentLabel = new Label($this);
        $this->CommentLabel->Name = "CommentLabel";
        $this->CommentLabel->Parent = $this;
        $this->CommentLabel->Left = 20;
        $this->CommentLabel->Top = $baseTop + $rowHeight * 5;
        $this->CommentLabel->Caption = "Kommentar:";

        $this->CommentMemo = new Memo($this);
        $this->CommentMemo->Name = "CommentMemo";
        $this->CommentMemo->Parent = $this;
        $this->CommentMemo->Left = $inputLeft;
        $this->CommentMemo->Top = $baseTop + $rowHeight * 5 - 3;
        $this->CommentMemo->Width = 350;
        $this->CommentMemo->Height = 100;
    }

    public function createButtonSection(): void
    {
        $buttonTop = 350;

        // Submit Button
        $this->SubmitButton = new Button($this);
        $this->SubmitButton->Name = "SubmitButton";
        $this->SubmitButton->Parent = $this;
        $this->SubmitButton->Left = 150;
        $this->SubmitButton->Top = $buttonTop;
        $this->SubmitButton->Width = 120;
        $this->SubmitButton->Caption = "Absenden";
        $this->SubmitButton->OnClick = "SubmitButtonClick";

        // Reset Button
        $this->ResetButton = new Button($this);
        $this->ResetButton->Name = "ResetButton";
        $this->ResetButton->Parent = $this;
        $this->ResetButton->Left = 280;
        $this->ResetButton->Top = $buttonTop;
        $this->ResetButton->Width = 120;
        $this->ResetButton->Caption = "Zuruecksetzen";
        $this->ResetButton->ButtonType = btReset;
    }

    public function createOutputSection(): void
    {
        // Ergebnis-Anzeige
        $this->ResultLabel = new Label($this);
        $this->ResultLabel->Name = "ResultLabel";
        $this->ResultLabel->Parent = $this;
        $this->ResultLabel->Left = 20;
        $this->ResultLabel->Top = 400;
        $this->ResultLabel->Width = 500;
        $this->ResultLabel->Caption = "";
        $this->ResultLabel->Font->Size = "12px";
        $this->ResultLabel->Font->Color = "#006600";
        $this->ResultLabel->HtmlContent = true;
    }

    // Event-Handler für Submit
    public function SubmitButtonClick(object $sender, array $params): void
    {
        $output = "<strong>Formulardaten empfangen:</strong><br/>";

        // Anrede ermitteln
        $gender = "Nicht angegeben";
        if ($this->GenderMale->Checked) {
            $gender = "Herr";
        } elseif ($this->GenderFemale->Checked) {
            $gender = "Frau";
        } elseif ($this->GenderOther->Checked) {
            $gender = "Divers";
        }
        $output .= "Anrede: " . htmlspecialchars($gender) . "<br/>";

        // Name
        $name = $this->NameEdit->Text;
        $output .= "Name: " . htmlspecialchars($name) . "<br/>";

        // Email
        $email = $this->EmailEdit->Text;
        $output .= "E-Mail: " . htmlspecialchars($email) . "<br/>";

        // Land
        $country = $this->CountryCombo->ItemIndex >= 0
            ? $this->CountryCombo->Items[$this->CountryCombo->ItemIndex]
            : "Nicht ausgewaehlt";
        $output .= "Land: " . htmlspecialchars($country) . "<br/>";

        // Newsletter
        $newsletter = $this->NewsletterCheck->Checked ? "Ja" : "Nein";
        $output .= "Newsletter: " . $newsletter . "<br/>";

        // Kommentar
        $comment = $this->CommentMemo->Text;
        if (!empty($comment)) {
            $output .= "Kommentar: " . htmlspecialchars($comment) . "<br/>";
        }

        $this->ResultLabel->Caption = $output;
    }
}

// Seite erstellen und anzeigen
$application = Application::getInstance();
$page = new AdvancedDemoPage($application);
$page->preinit();  // Formular-Werte lesen
$page->init();     // Events verarbeiten
$page->show();
