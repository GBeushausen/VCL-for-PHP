<?php
/**
 * VCL for PHP - Einfache Demo
 *
 * Diese Datei demonstriert die grundlegende Nutzung des VCL-Frameworks.
 * Aufruf: http://vcl.ddev.site/demo_simple.php
 */

declare(strict_types=1);

// Composer Autoloader einbinden
require_once(__DIR__ . '/../vendor/autoload.php');

// Namespaces importieren
use VCL\Forms\Page;
use VCL\Forms\Application;
use VCL\StdCtrls\Label;
use VCL\StdCtrls\Edit;
use VCL\StdCtrls\Button;
use VCL\ExtCtrls\Shape;

// Eigene Page-Klasse definieren
class SimpleDemoPage extends Page
{
    public ?Shape $Circle1 = null;
    public ?Label $Label1 = null;
    public ?Edit $Edit1 = null;
    public ?Button $Button1 = null;
    public ?Label $OutputLabel = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->Name = "SimpleDemoPage";
        $this->Caption = "VCL Simple Demo";
        $this->Color = "#f5f5f5";

        // Dekorativer Kreis (rechte Seite)
        $this->Circle1 = new Shape($this);
        $this->Circle1->Name = "Circle1";
        $this->Circle1->Parent = $this;
        $this->Circle1->Left = 400;
        $this->Circle1->Top = 30;
        $this->Circle1->Width = 80;
        $this->Circle1->Height = 80;
        $this->Circle1->Shape = "stCircle";
        $this->Circle1->Brush->Color = "#FF0000";
        $this->Circle1->Pen->Color = "#000000";
        $this->Circle1->Pen->Width = 2;

        // Label erstellen
        $this->Label1 = new Label($this);
        $this->Label1->Name = "Label1";
        $this->Label1->Parent = $this;
        $this->Label1->Left = 20;
        $this->Label1->Top = 30;
        $this->Label1->Caption = "Gib deinen Namen ein:";

        // Eingabefeld erstellen
        $this->Edit1 = new Edit($this);
        $this->Edit1->Name = "Edit1";
        $this->Edit1->Parent = $this;
        $this->Edit1->Left = 20;
        $this->Edit1->Top = 60;
        $this->Edit1->Width = 200;
        $this->Edit1->Text = "";

        // Button erstellen
        $this->Button1 = new Button($this);
        $this->Button1->Name = "Button1";
        $this->Button1->Parent = $this;
        $this->Button1->Left = 230;
        $this->Button1->Top = 58;
        $this->Button1->Caption = "Klick mich!";
        $this->Button1->OnClick = "Button1Click";

        // Ausgabe-Label
        $this->OutputLabel = new Label($this);
        $this->OutputLabel->Name = "OutputLabel";
        $this->OutputLabel->Parent = $this;
        $this->OutputLabel->Left = 20;
        $this->OutputLabel->Top = 110;
        $this->OutputLabel->Width = 350;
        $this->OutputLabel->Caption = "";
        $this->OutputLabel->Font->Size = "14px";
        $this->OutputLabel->Font->Color = "#0066cc";
    }

    // Event-Handler für den Button-Click
    public function Button1Click(object $sender, array $params): void
    {
        $name = $this->Edit1->Text;
        if (!empty($name)) {
            $this->OutputLabel->Caption = "Hallo, " . htmlspecialchars($name) . "! Willkommen bei VCL for PHP.";
        } else {
            $this->OutputLabel->Caption = "Bitte gib einen Namen ein.";
        }
    }
}

// Seite erstellen und anzeigen
$application = Application::getInstance();
$page = new SimpleDemoPage($application);
$page->show();
