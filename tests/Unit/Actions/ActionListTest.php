<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Actions;

use PHPUnit\Framework\TestCase;
use VCL\Actions\ActionList;

class ActionListTest extends TestCase
{
    private ActionList $actionList;

    protected function setUp(): void
    {
        $this->actionList = new ActionList();
        $this->actionList->Name = 'TestActionList';
    }

    public function testActionsProperty(): void
    {
        $actions = ['show', 'edit', 'delete'];
        $this->actionList->Actions = $actions;
        $this->assertSame($actions, $this->actionList->Actions);
    }

    public function testAddAction(): void
    {
        $this->actionList->addAction('create');
        $this->assertContains('create', $this->actionList->Actions);
    }

    public function testDeleteAction(): void
    {
        $this->actionList->Actions = ['show', 'edit', 'delete'];
        $this->actionList->deleteAction('edit');
        $this->assertNotContains('edit', $this->actionList->Actions);
        $this->assertContains('show', $this->actionList->Actions);
        $this->assertContains('delete', $this->actionList->Actions);
    }

    public function testHasAction(): void
    {
        $this->actionList->Actions = ['show', 'edit'];
        $this->assertTrue($this->actionList->hasAction('show'));
        $this->assertFalse($this->actionList->hasAction('delete'));
    }

    public function testOnExecuteEvent(): void
    {
        $this->actionList->OnExecute = 'handleAction';
        $this->assertSame('handleAction', $this->actionList->OnExecute);
    }

    public function testExpandActionToURL(): void
    {
        $this->actionList->Actions = ['show'];
        $url = 'page.php';
        $result = $this->actionList->expandActionToURL('show', $url);
        $this->assertTrue($result);
        $this->assertStringContainsString('TestActionList=show', $url);
    }

    public function testExpandActionToURLWithExistingQuery(): void
    {
        $this->actionList->Actions = ['show'];
        $url = 'page.php?id=1';
        $this->actionList->expandActionToURL('show', $url);
        $this->assertStringContainsString('&TestActionList=show', $url);
    }

    public function testExpandActionToURLReturnsFalseForInvalidAction(): void
    {
        $this->actionList->Actions = ['show'];
        $url = 'page.php';
        $result = $this->actionList->expandActionToURL('invalid', $url);
        $this->assertFalse($result);
    }

    public function testGetActionURL(): void
    {
        $_SERVER['PHP_SELF'] = '/index.php';
        $this->actionList->Actions = ['show'];
        $url = $this->actionList->getActionURL('show');
        $this->assertStringContainsString('TestActionList=show', $url);
    }

    public function testGetActionURLReturnsNullForInvalidAction(): void
    {
        $this->actionList->Actions = ['show'];
        $this->assertNull($this->actionList->getActionURL('invalid'));
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->actionList);
    }
}
