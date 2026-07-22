<?php

namespace SimpleCMP\WpSimpleCmp\Service;

use SimpleCMP\WpSimpleCmp\Database\Repositories\ServiceRepository;
use SimpleCMP\WpSimpleCmp\Library\ServicesLibrary;

class RegistryListPresenter
{
    private ServiceRepository $serviceRepo;
    private ServicesLibrary $library;

    public function __construct()
    {
        global $wpdb;
        $this->serviceRepo = new ServiceRepository($wpdb);
        $this->library = new ServicesLibrary();
    }

    /**
     * Determines the source tag for a service row.
     *
     * @return string 'eigene' | 'aus_bibliothek' | 'verwaist'
     */
    public function determineSource(object $service): string
    {
        if (!empty($service->library_adopted_at) && $service->library_adopted_at > 0) {
            // Was adopted from library — check if still exists there
            $libraryEntry = $this->library->find($service->service_id);
            if ($libraryEntry === null) {
                return 'verwaist';
            }
            return 'aus_bibliothek';
        }

        return 'eigene';
    }
}
