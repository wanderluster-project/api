# Wanderluster
> Documenting our natural world

#### Project Overview
Wanderluster is an open data project, similar to WikiData and OpenStreetMap where anyone may upload data about our natural world.  Wanderluster is an open platform to describe geographical entities such as mountains, streams, trails, parks as well as the plants, animals, and human impact on our wilderness.  It is an open platform and is community driven.

#### Data Model
Wanderluster stores data about entities (which is just a thing or an idea).  An entity can be anything that the Wanderluster community might be interested in. Attached to entities are properties.  Each property and entity have a universally unique identifier and have a URI associated with them.

Consider Mt Rainier which is a volcanic mountain near Seattle.  In our data model, Mt. Rainier would be an entity.  It has a number of properties that we can associate with it, for instance, it is a mountain, it has an altitude of 14,411 ft, and is a part of the Cascade Mountain range.

Say the UUID for Mt. Rainier is 621e95cc-9f83-11e9-a2a3-2a2ae2dbcce4 then a unique URI for Mt. Rainier would be...

    https://data.wanderluster.io/entity/621e95cc-9f83-11e9-a2a3-2a2ae2dbcce4/mount-rainier

Loading this URI in your web browser you would see a JSON document...

    {
        "@id": "https://data.wanderluster.io/entity/621e95cc-9f83-11e9-a2a3-2a2ae2dbcce4/mount-rainier"
        "_uuid": "621e95cc-9f83-11e9-a2a3-2a2ae2dbcce4",
        "_slug": "mount-rainier"
        "_label": "Mount Rainier",
        "_last_modified": "2019-01-12 12:34:00",
        "type_of": [
            "951eba44-9f86-11e9-a2a3-2a2ae2dbcce4/mountain",
            "103daf3c-9f87-11e9-a2a3-2a2ae2dbcce4/volcano"
        ],
        "wikipedia_url": "https://en.wikipedia.org/wiki/Mount_Rainier",
        "wikidata_qid": "Q194057",
        "altitude_m": 4392, 
        "altitude_ft": 14411,
        "mountain_range": "621e95cc-9f83-11e9-a2a3-2a2ae2dbcce4/cascade-mountains",
        "_exp:first_summited_by": [
            "c194ed2e-9f8a-11e9-a2a3-2a2ae2dbcce4/hazard-stevens",
            "03332fd4-9f8b-11e9-a2a3-2a2ae2dbcce4/p-b-van-trump"
        ]
    }

Notice the URI contains both the slug "mount-rainier" and the UUID "621e95cc-9f83-11e9-a2a3-2a2ae2dbcce4".  This is to help the data withing Wanderluster to be both robust for computational purposes as well as human readable.  Slugs can change between major versions.  If you need a stable URI that will never change, use the canonical URI for the entity....

    https://data.wanderluster.io/entity/621e95cc-9f83-11e9-a2a3-2a2ae2dbcce4

Notice also the experimental property "_exp:first_summited_by" is included.  Be careful working with experimental properties as they have not yet been approved by the community.  Their meaning may change at anytime.  Once a experimental property has been approved by the community, the property name will be updated at the next minor release.

Similar to entities, metadata about properties can also be found by looking up it's URI:

    https://data.wanderluster.io/prop/mountain_range
    
    {
        "description": "Range or subrange to which the geographical item belongs",
        "wikidata_pid": "P4552",
        "subject_types": ["entity/951eba44-9f86-11e9-a2a3-2a2ae2dbcce4/mountain"].
        "target_types": ["entity/f29cbd78-9f87-11e9-a2a3-2a2ae2dbcce4/mountain-range"],
        "multiple": True
    }

#### Extensible but Immutable Data 
Wanderluster builds on the ideas drawn from the semantic web and is extensible.  Anyone can say anything about anything.  As such, anyone may create new properties and they start out as 'Experimental'.  This allows for novel new ideas to be added to Wanderluster.  Once experimental properties get adopted by the community, they can be approved to be included in the next minor release.  Release schedule will be determined by the community (TBD).

#### Semantic Versioning and Backward Compatibility
- Experimental properties (prefixed with '_exp:') can be added at any time.
- New properties can be approved and added in minor version.
- Properties can be deprecated only at major version
- Properties can be removed at major version and only if already deprecated
- Property definitions can only be redefined at major version
- Entity UUIDs will never change
- Entity slugs may change but can only occur between major versions

#### Object Storage File Structure

    /{API-VERSION}/data/{UUID}/entity.json
    /{API-VERSION}/data/{UUID}/{VERSION}-entity.json
    /{API-VERSION}/data/{UUID}/{VERSION}-patch.json

#### Request/Response Cycle

1. Receive patch request
2. Authenticate and Authorize
3. Validate request
4. Obtain lock on entity
5. Calculate the new version of the entity
6. Save patch in object storage
7. Release lock
8. Return response
9. Trigger background job
    * Put job meta data in Job queue
    * Worker process starts job
    * Worker pulls previous version from object storage
    * Worker pulls patch version from object storage
    * Worker calculates new materialized entity
    * Materialized entity stored back in object storage

#### Services
- UuidGenerator - Responsible for generating a UUID of an entity
- LockManager - responsible for obtaining locks on entities
- VersionManager - responsible for getting the 'active' version of an entity
- PatchMerger - responsible for applying patch on entity and generating combined data
- PatchValidator - responsible for validating the semantics of a patch request
- EntityManager - responsible for retrieving and saving materialized entities
- PropertyManager - responsible for retrieving metadata for a property
- ObjectStorageManager - implements the storing/retrieving of entities from local storage, S3 or other sources





