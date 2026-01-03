<?php

declare(strict_types=1);

/**
 * htmx Demo - VCL for PHP 3.0
 *
 * This demo shows how to use htmx for AJAX functionality.
 *
 * htmx features:
 * - Uses HTML attributes (declarative) for AJAX behavior
 * - Returns HTML fragments that are swapped into the page
 * - Small footprint (~14KB minified)
 */

require_once __DIR__ . '/vendor/autoload.php';

use VCL\Forms\Application;
use VCL\Forms\Page;
use VCL\StdCtrls\Label;
use VCL\StdCtrls\Button;
use VCL\StdCtrls\Edit;

/**
 * Demo page showing htmx integration.
 */
class HtmxDemoPage extends Page
{
    public ?Label $Label1 = null;
    public ?Label $ResultLabel = null;
    public ?Button $Button1 = null;
    public ?Edit $Edit1 = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->Name = 'HtmxDemoPage';

        // Initialize click counter in session
        if (!isset($_SESSION['click_count'])) {
            $_SESSION['click_count'] = 0;
        }

        // Enable htmx (set to true for AJAX, false for traditional page reload)
        $this->UseHtmx = true;
        $this->UseHtmxDebug = true;

        // Create a label
        $this->Label1 = new Label($this);
        $this->Label1->Name = 'Label1';
        $this->Label1->Parent = $this;
        $this->Label1->Caption = 'Demo - Click the button (UseHtmx=' . ($this->UseHtmx ? 'true' : 'false') . ')';
        $this->Label1->Left = 20;
        $this->Label1->Top = 20;

        // Create an edit field
        $this->Edit1 = new Edit($this);
        $this->Edit1->Name = 'Edit1';
        $this->Edit1->Parent = $this;
        $this->Edit1->Left = 20;
        $this->Edit1->Top = 60;
        $this->Edit1->Width = 200;
        $this->Edit1->Text = 'Enter text here';

        // Create a button
        $this->Button1 = new Button($this);
        $this->Button1->Name = 'Button1';
        $this->Button1->Parent = $this;
        $this->Button1->Caption = 'Click Me';
        $this->Button1->Left = 20;
        $this->Button1->Top = 100;
        $this->Button1->OnClick = 'Button1Click';

        // Result label for traditional mode (without htmx)
        $this->ResultLabel = new Label($this);
        $this->ResultLabel->Name = 'ResultLabel';
        $this->ResultLabel->Parent = $this;
        $this->ResultLabel->Caption = '';
        $this->ResultLabel->Left = 20;
        $this->ResultLabel->Top = 140;
    }

    /**
     * Button click event handler.
     *
     * Called when the button is clicked via htmx.
     * Returns HTML that will be swapped into the target element.
     */
    public function Button1Click(object $sender, array $params): string
    {
        $_SESSION['click_count']++;
        $clickCount = $_SESSION['click_count'];
        $text = $_POST['Edit1'] ?? 'No text entered';
        $time = date('H:i:s');

        return sprintf(
            '<div class="result-item" style="padding: 10px; margin: 5px 0; background: #e0ffe0; border-radius: 5px;">' .
            '<strong>Click #%d</strong> at %s<br>' .
            'You entered: <em>%s</em>' .
            '</div>',
            $clickCount,
            $time,
            htmlspecialchars($text)
        );
    }
}

// Create and run the application
$application = Application::getInstance();
$page = new HtmxDemoPage($application);
$page->preinit();
$page->init();
$page->show();
