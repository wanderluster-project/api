<?php

declare(strict_types=1);

namespace App\Exception;

class ErrorMessages
{
    const INVALID_ENTITY_TYPE = 'Invalid Entity Type - %s.';
    const INVALID_ENTITY_DATA = 'Invalid Entity Data.';
    const INVALID_EXT = 'Invalid File Extension - %s.';
    const INVALID_MIMETYPE = 'Invalid MimeType - %s.';
    const INVALID_S3_BUCKET = 'Invalid S3 Bucket - %s.';
    const INVALID_ENTITY_ID = 'Invalid EntityID format - %s.';
    const INVALID_SNAPSHOT_ID = 'Invalid SnapshotId format - %s.';
    const INVALID_LANGUAGE_CODE = 'Invalid language code - %s.';
    const INVALID_USERNAME = 'Invalid username - %s.';
    const INVALID_JSON = 'Invalid JSON passed as argument.';
    const OPTION_REQUIRED = 'Configuration option missing - %s.';
    const UNAGLE_DETERMINE_TYPE = 'Unable to determine the type for key - %s.';

    const INVALID_DATA_TYPE_VALUE = 'Invalid value passed to %s data type.';
    const ERROR_HYDRATING_DATATYPE = 'Error hydrating %s data type - %s.';

    const REQUEST_MISSING_PARAMETER = 'Missing parameter: %s.';
    const REQUEST_INVALID_FILE = 'Invalid or corrupt file uploaded.';
    const SERVER_ERROR_UPLOADING = 'Error encountered saving file.  Please try again later.';
    const SERVER_ERROR_DELETING = 'Error encountered deleting file.';

    const SERIALIZATION_ERROR = 'Error serializing - %s.';
    const DESERIALIZATION_ERROR = 'Error deserializing - %s.';

    const METHOD_NOT_IMPLEMENTED = 'Method not implemented yet - %s.';
    const ENTITY_LANGUAGE_NOT_SET = 'Entity language not set.';
    const UNABLE_TO_USE_ANY_LANGUAGE = 'You must specify a language.  Wildcard (*) is not allowed).';
    const LANGUAGE_REQUIRED = 'You must specify a language.';

    const JWT_KEYS_MISSING = 'JWT Keys Missing.';
    const JWT_INVALID = 'Invalid JWT.';

    const TOMBSTONE_EDIT = 'Unable to change tombstone type.';
    const VERSION_INVALID = 'Invalid version: %s.';
    const UNABLE_TO_SET_VERSION = 'Unable to set version for data type: %s';
    const UNABLE_TO_COMPARE = 'Unable to use comparisons with data type: %s.';
    const MERGE_UNSUCCESSFUL = 'Unable to merge %s with %s.';
    const DATA_TYPE_COMPARISON_UNSUCCESSFUL = 'Unable to compare %s with %s.';
    const DATA_TYPE_ALREADY_REGISTERED = 'Data type already registered with Serializer - %s.';
    const DATA_TYPE_UNKOWN = 'Data type unknown - %s';
    const UNKNOWN_ATTRIBUTE_NAME = 'Unknown attribute - %s';
}
