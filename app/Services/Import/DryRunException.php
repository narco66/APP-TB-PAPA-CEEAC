<?php

namespace App\Services\Import;

/** Exception sentinelle pour le mode dry-run (rollback sans erreur métier). */
class DryRunException extends \RuntimeException {}
