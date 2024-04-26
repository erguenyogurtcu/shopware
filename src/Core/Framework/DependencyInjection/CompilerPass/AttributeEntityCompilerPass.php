<?php

namespace Shopware\Core\Framework\DependencyInjection\CompilerPass;

use Shopware\Core\Framework\DataAbstractionLayer\Attribute\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\Field;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\FieldType;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\ManyToMany;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\ManyToOne;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\OneToMany;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\OneToOne;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\Primary;
use Shopware\Core\Framework\DataAbstractionLayer\AttributeEntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEventFactory;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Read\EntityReaderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntityAggregatorInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearcherInterface;
use Shopware\Core\Framework\DataAbstractionLayer\VersionManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AttributeEntityCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $services = $container->findTaggedServiceIds('shopware.entity');

        foreach ($services as $class => $_) {
            $reflection = new \ReflectionClass($class);

            $collection = $reflection->getAttributes(Entity::class);

            /** @var Entity $instance */
            $instance = $collection[0]->newInstance();

            $meta = [
                'entity_class' => $class,
                'entity_name' => $instance->name,
                'fields' => $this->parse($reflection)
            ];

            $definition = new Definition(AttributeEntityDefinition::class);
            $definition->addArgument($meta);
            $definition->setPublic(true);
            $container->setDefinition($instance->name . '.definition', $definition);

            $repository = new Definition(
                EntityRepository::class,
                [
                    new Reference($instance->name . '.definition'),
                    new Reference(EntityReaderInterface::class),
                    new Reference(VersionManager::class),
                    new Reference(EntitySearcherInterface::class),
                    new Reference(EntityAggregatorInterface::class),
                    new Reference('event_dispatcher'),
                    new Reference(EntityLoadedEventFactory::class),
                ]
            );
            $repository->setPublic(true);

            $container->setDefinition($instance->name . '.repository', $repository);

            $registry = $container->getDefinition(DefinitionInstanceRegistry::class);
            $registry->addMethodCall('register', [new Reference($instance->name . '.definition'), $instance->name . '.definition']);
        }
    }

    private function parse(\ReflectionClass $reflection): array
    {
        $properties = $reflection->getProperties();

        $fields = [];
        foreach ($properties as $property) {
            $attribute = $this->getFieldAttribute($property);

            if ($attribute === null) {
                continue;
            }

            $field = $this->parseField($property, $attribute);

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * @return \ReflectionAttribute<Field>|null
     */
    private function getFieldAttribute(\ReflectionProperty $property): ?\ReflectionAttribute
    {
        $list = [Field::class, Primary::class, OneToMany::class, ManyToMany::class, ManyToOne::class, OneToOne::class];

        foreach ($list as $attribute) {
            $attribute = $property->getAttributes($attribute);
            if (!empty($attribute)) {
                return $attribute[0];
            }
        }

        return null;
    }

    private function parseField(\ReflectionProperty $property, \ReflectionAttribute $attribute): array
    {
        /** @var Field $field */
        $field = $attribute->newInstance();

        $field->column = !empty($field->column) ? $field->column : $property->getName();

        $field->nullable = $property->getType()?->allowsNull() ?? true;

        $meta = [
            'flags' => []
        ];

        if (!$field->nullable) {
            $meta['flags'][Required::class] = ['class' => Required::class];
        }

        if ($field instanceof Primary) {
            $meta['flags'][PrimaryKey::class] = ['class' => PrimaryKey::class];
            $meta['flags'][Required::class] = ['class' => Required::class];
        }

        if ($field->type === FieldType::STRING) {
            $meta['class'] = StringField::class;
            $meta['args'] = [$field->column, $property->getName()];
        }

        if ($field->type === FieldType::UUID) {
            $meta['class'] = IdField::class;
            $meta['args'] = [$field->column, $property->getName()];
        }

        if ($field->type === FieldType::FLOAT) {
            $meta['class'] = FloatField::class;
            $meta['args'] = [$field->column, $property->getName()];
        }

        if ($field->type === FieldType::INT) {
            $meta['class'] = IntField::class;
            $meta['args'] = [$field->column, $property->getName()];
        }

        if ($field->type === FieldType::BOOL) {
            $meta['class'] = BoolField::class;
            $meta['args'] = [$field->column, $property->getName()];
        }

        if ($field->type === FieldType::DATETIME) {
            $meta['class'] = DateTimeField::class;
            $meta['args'] = [$field->column, $property->getName()];
        }

        return $meta;
    }
}
