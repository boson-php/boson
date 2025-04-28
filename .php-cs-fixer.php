<?php

$files = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src'
    ])
    ->filter(static fn (\SplFileInfo $file): bool
        => !\in_array(\realpath($file->getPathname()), [
            // "public private(set) ?Window $default" detects as ternary operator
            \realpath(__DIR__ . '/src/Window/Manager/WindowManager.php'),
            // Fixer doesnt support an abstract properties
            \realpath(__DIR__ . '/src/Shared/IdValueGenerator/IntValueGenerator.php'),
            \realpath(__DIR__ . '/src/Http/Headers/ContentDispositionHeader.php'),
        ], true)
    )
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS2.0' => true,
        '@PER-CS2.0:risky' => true,
        // broken in v3.70.2 (`$property { get; set; }` -> `$property { get; }`)
        'no_empty_statement' => false,
        // broken in v3.70.2 (`public private(set) Some $x` -> `public private(set) public Some $x`)
        'visibility_required' => false,
        'strict_param' => true,
        'align_multiline_comment' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'backtick_to_shell_exec' => true,
        'binary_operator_spaces' => true,
        'blank_line_before_statement' => [
            'statements' => [
                'return',
            ],
        ],
        'braces_position' => [
            'allow_single_line_anonymous_functions' => true,
            'allow_single_line_empty_anonymous_classes' => true,
        ],
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
            ],
        ],
        'class_reference_name_casing' => true,
        'clean_namespace' => true,
        'declare_parentheses' => true,
        'echo_tag_syntax' => true,
        'empty_loop_body' => ['style' => 'braces'],
        'empty_loop_condition' => true,
        'fully_qualified_strict_types' => true,
        'general_phpdoc_tag_rename' => [
            'replacements' => [
                'inheritDocs' => 'inheritDoc',
            ],
        ],
        'global_namespace_import' => [
            'import_classes' => false,
            'import_constants' => false,
            'import_functions' => false,
        ],
        'include' => true,
        'increment_style' => true,
        'integer_literal_case' => true,
        'lambda_not_used_import' => true,
        'linebreak_after_opening_tag' => true,
        'magic_constant_casing' => true,
        'magic_method_casing' => true,
        'native_function_casing' => true,
        'native_type_declaration_casing' => true,
        'no_alias_language_construct_call' => true,
        'no_alternative_syntax' => true,
        'no_binary_string' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_extra_blank_lines' => [
            'tokens' => [
                'attribute',
                'case',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'parenthesis_brace_block',
                'square_brace_block',
                'switch',
                'throw',
                'use',
            ],
        ],
        'no_leading_namespace_whitespace' => true,
        'no_mixed_echo_print' => true,
        'no_null_property_initialization' => true,
        'no_short_bool_cast' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_spaces_around_offset' => true,
        'no_superfluous_phpdoc_tags' => [
            'allow_hidden_params' => true,
            'remove_inheritdoc' => true,
        ],
        'no_trailing_comma_in_singleline' => true,
        'no_unneeded_braces' => [
            'namespaces' => true,
        ],
        'no_unneeded_control_parentheses' => [
            'statements' => [
                'break',
                'clone',
                'continue',
                'echo_print',
                'others',
                'return',
                'switch_case',
                'yield',
                'yield_from',
            ],
        ],
        'no_unneeded_import_alias' => true,
        'no_unset_cast' => true,
        'no_unused_imports' => true,
        'no_useless_concat_operator' => true,
        'no_useless_nullsafe_operator' => true,
        'no_whitespace_before_comma_in_array' => true,
        'normalize_index_brace' => true,
        'nullable_type_declaration' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'object_operator_without_whitespace' => true,
        'operator_linebreak' => [
            'only_booleans' => true,
        ],
        'ordered_imports' => [
            'imports_order' => [
                'class',
                'function',
                'const',
            ],
            'sort_algorithm' => 'alpha',
        ],
        'ordered_types' => [
            'null_adjustment' => 'always_last',
            'sort_algorithm' => 'none',
        ],
        'php_unit_fqcn_annotation' => true,
        'php_unit_method_casing' => true,
        'phpdoc_align' => false,
        'phpdoc_annotation_without_dot' => true,
        'phpdoc_indent' => true,
        'phpdoc_inline_tag_normalizer' => true,
        'phpdoc_no_access' => true,
        'phpdoc_no_alias_tag' => false,
        'phpdoc_no_package' => true,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_order' => [
            'order' => [
                'api',
                'template',
                'template-extends',
                'extends',
                'template-implements',
                'implements',
                'property',
                'property-read',
                'property-write',
                'psalm-require-implements',
                'phpstan-require-implements',
                'psalm-require-extends',
                'phpstan-require-extends',
                'mixin',
                'readonly',
                'psalm-readonly',
                'phpstan-readonly',
                'param',
                'return',
                'throws',
                'internal',
                'psalm-internal',
                'psalm-suppress',
            ],
        ],
        'phpdoc_return_self_reference' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => [
            'groups' => [
                [
                    'property',
                    'property-read',
                    'property-write',
                ],
                ['internal', 'psalm-internal', 'phpstan-internal'],
                [
                    'template',
                    'template-extends',
                    'extends',
                    'template-implements',
                    'implements',
                    'psalm-require-implements',
                    'phpstan-require-implements',
                    'psalm-require-extends',
                    'phpstan-require-extends',
                    'mixin',
                ],
                ['psalm-taint-sink', 'param'],
                ['return', 'throws'],
                ['psalm-suppress'],
            ],
        ],
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_tag_type' => [
            'tags' => [
                'inheritDoc' => 'inline',
            ],
        ],
        'phpdoc_to_comment' => false,
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types' => true,
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
            'sort_algorithm' => 'none',
        ],
        'phpdoc_var_without_name' => true,
        'semicolon_after_instruction' => true,
        'simple_to_complex_string_variable' => true,
        'single_class_element_per_statement' => true,
        'single_import_per_statement' => true,
        'single_line_comment_spacing' => true,
        'single_line_comment_style' => [
            'comment_types' => [
                'hash',
            ],
        ],
        'single_line_throw' => false,
        'single_quote' => true,
        'space_after_semicolon' => [
            'remove_in_empty_for_expressions' => true,
        ],
        'standardize_increment' => true,
        'standardize_not_equals' => true,
        'switch_continue_to_break' => true,
        'trailing_comma_in_multiline' => true,
        'trim_array_spaces' => true,
        'type_declaration_spaces' => true,
        'types_spaces' => true,
        'unary_operator_spaces' => true,
        'whitespace_after_comma_in_array' => true,
        'yoda_style' => false,
    ])
    ->setCacheFile(__DIR__ . '/vendor/.cache.php-cs-fixer')
    ->setFinder($files);
