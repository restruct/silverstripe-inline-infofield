<?php

namespace Restruct\IiBrowser;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;

/**
 * BROWSER-TEST FIXTURE ONLY - gives a spec a page to open: GET /admin/iib-reset/reseed?title=...
 * answers {"id": the IiBPage ID} (an existing page of that title is reused); with &record=1 also
 * "recordId", an IiBRecord on that page.
 *
 * A LeftAndMain because the admin routes those by url_segment with no YAML (the fixtures are copied
 * into app/src/). LeftAndMain's own access check applies, so only the logged-in admin can call it.
 * Never loaded by a real install (tests/browser/ carries a _manifest_exclude marker).
 */
class IiBResetAdmin extends LeftAndMain
{
    private static $url_segment = 'iib-reset';

    private static $menu_title = 'Infofield browser reset';

    private static $allowed_actions = ['reseed'];

    public function reseed(HTTPRequest $request): HTTPResponse
    {
        $title = (string) $request->getVar('title');
        if ($title === '') {
            return $this->httpError(400, 'title is required');
        }
        $page = IiBPage::get()->filter('Title', $title)->first();
        if (!$page) {
            $page = IiBPage::create(['Title' => $title]);
            $page->write();
        }
        $data = ['id' => $page->ID];
        # ?record=1 also gives the page one IiBRecord, for the GridField detail form spec (#3).
        if ($request->getVar('record')) {
            $record = $page->Records()->first();
            if (!$record) {
                $record = IiBRecord::create(['Title' => 'A record', 'Sub_Title' => 'Decoy']);
                $record->PageID = $page->ID;
                $record->write();
            }
            $data['recordId'] = $record->ID;
        }

        return HTTPResponse::create(json_encode($data))
            ->addHeader('Content-Type', 'application/json');
    }
}
