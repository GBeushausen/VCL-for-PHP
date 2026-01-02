<?php

declare(strict_types=1);

namespace VCL\Buttons\Enums;

/**
 * Button kind enumeration.
 *
 * Specifies the kind of bitmap button with predefined images.
 */
enum ButtonKind: string
{
    case Custom = 'bkCustom';
    case OK = 'bkOK';
    case Cancel = 'bkCancel';
    case Yes = 'bkYes';
    case No = 'bkNo';
    case Help = 'bkHelp';
    case Close = 'bkClose';
    case Abort = 'bkAbort';
    case Retry = 'bkRetry';
    case Ignore = 'bkIgnore';
    case All = 'bkAll';

    /**
     * Get the default image path for this button kind.
     */
    public function getImagePath(): string
    {
        return match($this) {
            self::Custom => '',
            self::OK => '/images/ok.gif',
            self::Cancel => '/images/cancel.gif',
            self::Yes => '/images/yes.gif',
            self::No => '/images/no.gif',
            self::Help => '/images/help.gif',
            self::Close => '/images/close.gif',
            self::Abort => '/images/abort.gif',
            self::Retry => '/images/retry.gif',
            self::Ignore => '/images/ignore.gif',
            self::All => '/images/all.gif',
        };
    }
}
