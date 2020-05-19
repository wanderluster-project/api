# Data Model
Since Wanderluster is a community project, its data model is designed to be flexible to grow and change as information is continually updated.  Roughly there are four things that define the data model - entities, entity type, attributes, and values.  These combine together to provide rich information and build up a graph.

Entities are object that the community wants to describe.  It could be geographical objects like mountains, rivers, or hiking trails.  Enties can also represent people, buildings, animals, or organizations.  Basically anything a user is interested in can be represented under the hood by an Entity.  Entities are like the Nouns in our data model.

Entity Types are a numeric index that categorizes the entity.  There are thousands of entity types (mountain, river, trail, beach, bear, ice cave, etc..).  The entity type provides semantic understanding of what the entity is.  An entity can only be assigned to one entity type.

Attributes and values are properties of the entities, that describe the entity or link entities together.  For instance, Elevation is an attribute that can be added to a Mountain entity.  Attributes can be added/removed/changed by the Wanderluster community.   Attributes are like the adjectives that describe our Entities.

Consider how we would model Mount Rainier:

    Entity:     Mount Rainier
    EntityType: Mountain

Similar to how a database stores numbers differently from text, Wanderluster has different attribute types.  This allows Wanderluster to understand and store data better.

Also, since Wanderluster is multi-language by design, some attribute values can be localized into different languages.

    Elevation:  
        Type: Distance(Feet)
        Value: 14,411
        
    Wikipedia:  
        Type: URL
        Value: https://en.wikipedia.org/wiki/Mount_Rainier
        
    Mountain Range: 
        Type: Entity
        Value: Link to the Cascades Mountain Range Entity.
        
    Mountain Type:
        Type: Entity
        Value: Link to the Stratovolcano Entity.

Wanderluster has rich collection of attribute types.  For more information see [attribute-types](attribute-types.md)



## Example: Creating the Mount Rainier Entity using PHP

Lets recreate the information for Mount Rainier using our PHP classes.


    <?php
    
    use App\DataModel\Entity\Entity;
    use App\DataModel\Entity\EntityTypes;
    use App\DataModel\Translation\LanguageCodes;
    use App\DataModel\Types\Distance;
    use App\DataModel\Types\Text;
    use App\DataModel\Types\Url;
    
    $app = new Wanderluster();
    $em = $app->getEntityManager();
    
    $cascades = $em->find(EntityTypes::MOUNTAIN_RANGE,'cascades');
    $stratovolcano = $em->find(EntityTypes::MOUNTAIN_TYPE,'stratovolcano');
    
    // Create new entity
    $mtRainier = $em->create(EntityTypes::MOUNTAIN);
    $mtRainer->load(
        [
            'dist' => DistanceCodes::FEET,
            'locale' => LocaleCodes::EN_US
        ]
    )
    $mtRainier->set('name', 'Mount Rainier'));
    $mtRainier->set('elevation', 14411);
    $mtRainier->set('wikipedia_url', 'https://en.wikipedia.org/wiki/Mount_Rainier');
    $mtRainier->set('mountain_range',$cascades);
    $mtRainier->set('mountain_type',$stratovolcano);
    
    // Commit changes
    $em->commit();

    




