<?php declare(strict_types=1);

namespace PslSearchFormTest\Controller;

class IndexControllerTest extends PslSearchFormControllerTestCase
{
    protected $site;

    public function setUp(): void
    {
        parent::setUp();

        $response = $this->api()->create('sites', [
            'o:title' => 'Test site',
            'o:slug' => 'test',
            'o:theme' => 'default',
        ]);

        $this->site = $response->getContent();

        $siteSettings = $this->getServiceLocator()->get('Omeka\Settings\Site');
        $siteSettings->setTargetId($this->site->id());
        $siteSettings->set('search_pages', [$this->searchPage->id()]);

        $this->resetApplication();
    }

    public function tearDown(): void
    {
        $this->api()->delete('sites', $this->site->id());

        parent::tearDown();
    }

    public function testSearchAction(): void
    {
        $this->dispatch('/s/test/test/search');
        $this->assertResponseStatusCode(200);

        $this->assertQuery('input[name="q"]');
        $this->assertNotQuery('.search-results');
    }

    public function testSearchWithParamsAction(): void
    {
        $this->dispatch('/s/test/test/search', 'GET', ['q' => 'test']);
        $this->assertResponseStatusCode(200);
        $this->assertQuery('.search-results');
        $this->assertQuery('input[name="q"][value="test"]');
    }
}
