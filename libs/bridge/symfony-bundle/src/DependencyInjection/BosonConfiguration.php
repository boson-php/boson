<?php

declare(strict_types=1);

namespace Boson\Bridge\Symfony\DependencyInjection;

use Boson\Window\WindowCreateInfo;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @phpstan-type BosonConfigType array{
 *     name: non-empty-string,
 *     schemes: non-empty-list<non-empty-string>,
 *     is_debug: bool,
 *     is_quit_on_close: bool,
 *     window: array{
 *          entrypoint: non-empty-string,
 *          width: int<1, max>,
 *          height: int<1, max>,
 *          is_visible: bool,
 *          is_resizable: bool,
 *          is_always_on_top: bool,
 *          is_click_through: bool,
 *          decorations: 'default'|'dark_mode'|'frameless'|'transparent',
 *          flags: array<array-key, mixed>,
 *          storage: bool|non-empty-string|null,
 *          enable_context_menu: bool|null,
 *          enable_dev_tools: bool|null,
 *          ...
 *     },
 *     static: array{
 *         directory: list<non-empty-string>
 *     },
 *     ...
 * }
 */
final class BosonConfiguration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder<'array'>
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $tree = new TreeBuilder('boson');

        $root = $tree->getRootNode();

        $root->children()
            ->stringNode('name')
                ->info('The name of the boson application')
                ->cannotBeEmpty()
                ->defaultValue('boson')
            ->end()
            ->arrayNode('schemes')
                ->info('An URI schemes of the application')
                ->stringPrototype()
                    ->cannotBeEmpty()
                ->end()
                ->cannotBeEmpty()
                ->defaultValue(['boson'])
            ->end()
            ->booleanNode('is_debug')
                ->info('Enable or disable application debug mode')
                ->defaultValue('%kernel.debug%')
            ->end()
            ->booleanNode('is_quit_on_close')
                ->info('Exits the application when all windows are closed')
                ->defaultTrue()
            ->end()
            ->arrayNode('window')
                ->children()
                    ->scalarNode('entrypoint')
                        ->info('The entrypoint URI of the application')
                        ->cannotBeEmpty()
                        ->defaultValue('boson://localhost')
                    ->end()
                    ->integerNode('width')
                        ->info('Initial width of the default application window')
                        ->defaultValue(WindowCreateInfo::DEFAULT_WIDTH)
                    ->end()
                    ->integerNode('height')
                        ->info('Initial height of the default application window')
                        ->defaultValue(WindowCreateInfo::DEFAULT_HEIGHT)
                    ->end()
                    ->booleanNode('is_visible')
                        ->info('Sets the visibility of an application window after creation')
                        ->defaultTrue()
                    ->end()
                    ->booleanNode('is_resizable')
                        ->info('Allows to resize an application window')
                        ->defaultTrue()
                    ->end()
                    ->booleanNode('is_always_on_top')
                        ->info('Displays a window on top of other windows after creation')
                        ->defaultFalse()
                    ->end()
                    ->booleanNode('is_click_through')
                        ->info('Disable window response to mouse events')
                        ->defaultFalse()
                    ->end()
                    ->enumNode('decorations')
                        ->info('Sets decorations of an application window')
                        ->values(['default', 'dark_mode', 'frameless', 'transparent'])
                        ->defaultValue('default')
                    ->end()
                    ->arrayNode('flags')
                        ->info('The WebView flags of the application')
                        ->scalarPrototype()
                        ->end()
                    ->end()
                    ->booleanNode('enable_context_menu')
                        ->info('Enables or disabled context menu (by default depends on "debug" value)')
                        ->defaultNull()
                    ->end()
                    ->booleanNode('enable_dev_tools')
                        ->info('Enables or disabled dev tools (by default depends on "debug" value)')
                        ->defaultNull()
                    ->end()
                    ->scalarNode('storage')
                        ->info('Sets default WebView session storage directory')
                        ->cannotBeEmpty()
                        ->defaultFalse()
                    ->end()
                ->end()
                ->addDefaultsIfNotSet()
            ->end()
            ->arrayNode('static')
                ->children()
                    ->arrayNode('directory')
                        ->info('The directory of the static files')
                        ->stringPrototype()
                            ->cannotBeEmpty()
                        ->end()
                        ->defaultValue(['%kernel.project_dir%/public'])
                    ->end()
                ->end()
            ->end()
        ->end();

        return $tree;
    }
}
