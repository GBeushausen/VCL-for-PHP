<?php
/**
 * VCL for PHP - Einfache Demo
 *
 * Diese Datei demonstriert die grundlegende Nutzung des VCL-Frameworks.
 * Aufruf: http://vcl.ddev.site/demo_simple.php
 */

// Framework einbinden
require_once("vcl.inc.php");

// Benötigte Units laden
use_unit("forms.inc.php");
use_unit("stdctrls.inc.php");

// Eigene Page-Klasse definieren
class SimpleDemoPage extends Page
{
    public $Label1 = null;
    public $Edit1 = null;
    public $Button1 = null;
    public $OutputLabel = null;

    function __construct($aowner = null)
    {
        parent::__construct($aowner);

        $this->Caption = "VCL Simple Demo";
        $this->Color = "#f5f5f5";

        // Label erstellen
        $this->Label1 = new Label($this);
        $this->Label1->Name = "Label1";
        $this->Label1->Parent = $this;
        $this->Label1->Left = 20;
        $this->Label1->Top = 20;
        $this->Label1->Caption = "Gib deinen Namen ein:";

        // Eingabefeld erstellen
        $this->Edit1 = new Edit($this);
        $this->Edit1->Name = "Edit1";
        $this->Edit1->Parent = $this;
        $this->Edit1->Left = 20;
        $this->Edit1->Top = 50;
        $this->Edit1->Width = 200;
        $this->Edit1->Text = "";

        // Button erstellen
        $this->Button1 = new Button($this);
        $this->Button1->Name = "Button1";
        $this->Button1->Parent = $this;
        $this->Button1->Left = 230;
        $this->Button1->Top = 48;
        $this->Button1->Caption = "Klick mich!";
        $this->Button1->OnClick = "Button1Click";

        // Ausgabe-Label
        $this->OutputLabel = new Label($this);
        $this->OutputLabel->Name = "OutputLabel";
        $this->OutputLabel->Parent = $this;
        $this->OutputLabel->Left = 20;
        $this->OutputLabel->Top = 100;
        $this->OutputLabel->Caption = "";
        $this->OutputLabel->Font->Size = "14px";
        $this->OutputLabel->Font->Color = "#0066cc";
    }

    // Event-Handler für den Button-Click
    function Button1Click($sender, $params)
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
global $application;
$page = new SimpleDemoPage($application);
$page->show();
?>
