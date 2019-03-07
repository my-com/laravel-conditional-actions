<?php

// Add PHP CS Fixer to PHPStorm
// https://hackernoon.com/how-to-configure-phpstorm-to-use-php-cs-fixer-1844991e521f

// In case PHP CS Fixer fails without any explanation, there might be syntax errors in some files, try running
// ./vendor/bin/php-cs-fixer --allow-risky=yes --using-cache=no --verbose --diff fix --dry-run

$finder = PhpCsFixer\Finder::create()
    ->exclude(['bootstrap', 'database', 'storage', 'tests', 'vendor', 'app/Api'])
    ->notName('_ide_helper*.php')
    ->notName('server.php')
    ->in(__DIR__)
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'align_multiline_comment' => true,
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => ['statements' => ['break', 'continue', 'declare', 'die', 'do', 'exit', 'for', 'foreach', 'goto', 'if', 'include', 'include_once', 'require', 'require_once', 'return', 'switch', 'throw', 'try', 'while', 'yield']],
        'cast_spaces' => true,
        'class_attributes_separation' => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'compact_nullable_typehint' => true,
        'dir_constant' => true,
        'ereg_to_preg' => true,
        'explicit_indirect_variable' => true,
        'explicit_string_variable' => true,
        'function_to_constant' => true,
        'function_typehint_space' => true,
        'include' => true,
        'linebreak_after_opening_tag' => true,
        'magic_constant_casing' => true,
        'mb_str_functions' => true,
        'method_chaining_indentation' => true,
        'modernize_types_casting' => true,
        'multiline_whitespace_before_semicolons' => true,
        'native_function_casing' => true,
        'native_function_invocation' => true,
        'new_with_braces' => true,
        'no_alias_functions' => true,
        'no_alternative_syntax' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_blank_lines' => true,
        'no_homoglyph_names' => true,
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_mixed_echo_print' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_null_property_initialization' => true,
        'no_php4_constructor' => true,
        'no_short_bool_cast' => true,
        'no_short_echo_tag' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_spaces_around_offset' => true,
        'no_superfluous_elseif' => true,
        'no_trailing_comma_in_list_call' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unneeded_curly_braces' => true,
        'no_unneeded_final_method' => true,
        'no_unset_on_property' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
        'non_printable_character' => ['use_escape_sequences_in_strings' => true],
        'normalize_index_brace' => true,
        'object_operator_without_whitespace' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_annotation_without_dot' => true,
        'phpdoc_indent' => true,
        'phpdoc_inline_tag' => true,
        'phpdoc_no_access' => true,
        'phpdoc_no_alias_tag' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_no_package' => true,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_order' => true,
        'phpdoc_return_self_reference' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types' => true,
        'phpdoc_var_without_name' => true,
        'psr4' => true,
        'random_api_migration' => true,
        'return_assignment' => true,
        'return_type_declaration' => true,
        'self_accessor' => true,
        'semicolon_after_instruction' => true,
        'set_type_to_cast' => true,
        'short_scalar_cast' => true,
        'simplified_null_return' => true,
        'single_blank_line_before_namespace' => true,
        'single_line_comment_style' => true,
        'single_quote' => true,
        'space_after_semicolon' => ['remove_in_empty_for_expressions' => true],
        'standardize_increment' => true,
        'standardize_not_equals' => true,
        'strict_param' => true,
        'ternary_operator_spaces' => true,
        'ternary_to_null_coalescing' => true,
        'trailing_comma_in_multiline_array' => true,
        'trim_array_spaces' => true,
        'unary_operator_spaces' => true,
        'whitespace_after_comma_in_array' => true,
        'yoda_style' => true,
    ])
    ->setFinder($finder);

/*
This document has been generated with
https://mlocati.github.io/php-cs-fixer-configurator/
you can change this configuration by importing this YAML code:

fixerSets:
  - '@PSR2'
fixers:
  align_multiline_comment: true
  array_indentation: true
  array_syntax:
    syntax: short
  blank_line_after_opening_tag: true
  blank_line_before_statement:
    statements:
      - break
      - continue
      - declare
      - die
      - do
      - exit
      - for
      - foreach
      - goto
      - if
      - include
      - include_once
      - require
      - require_once
      - return
      - switch
      - throw
      - try
      - while
      - yield
  cast_spaces: true
  class_attributes_separation: true
  combine_consecutive_issets: true
  combine_consecutive_unsets: true
  compact_nullable_typehint: true
  dir_constant: true
  ereg_to_preg: true
  explicit_indirect_variable: true
  explicit_string_variable: true
  function_to_constant: true
  function_typehint_space: true
  include: true
  linebreak_after_opening_tag: true
  magic_constant_casing: true
  mb_str_functions: true
  method_chaining_indentation: true
  modernize_types_casting: true
  multiline_whitespace_before_semicolons: true
  native_function_casing: true
  native_function_invocation: true
  new_with_braces: true
  no_alias_functions: true
  no_alternative_syntax: true
  no_blank_lines_after_class_opening: true
  no_blank_lines_after_phpdoc: true
  no_empty_comment: true
  no_empty_phpdoc: true
  no_empty_statement: true
  no_extra_blank_lines: true
  no_homoglyph_names: true
  no_leading_import_slash: true
  no_leading_namespace_whitespace: true
  no_mixed_echo_print: true
  no_multiline_whitespace_around_double_arrow: true
  no_null_property_initialization: true
  no_php4_constructor: true
  no_short_bool_cast: true
  no_short_echo_tag: true
  no_singleline_whitespace_before_semicolons: true
  no_spaces_around_offset: true
  no_superfluous_elseif: true
  no_trailing_comma_in_list_call: true
  no_trailing_comma_in_singleline_array: true
  no_unneeded_control_parentheses: true
  no_unneeded_curly_braces: true
  no_unneeded_final_method: true
  no_unset_on_property: true
  no_unused_imports: true
  no_useless_else: true
  no_useless_return: true
  no_whitespace_before_comma_in_array: true
  no_whitespace_in_blank_line: true
  non_printable_character:
    use_escape_sequences_in_strings: true
  normalize_index_brace: true
  object_operator_without_whitespace: true
  phpdoc_add_missing_param_annotation: true
  phpdoc_annotation_without_dot: true
  phpdoc_indent: true
  phpdoc_inline_tag: true
  phpdoc_no_access: true
  phpdoc_no_alias_tag: true
  phpdoc_no_empty_return: true
  phpdoc_no_package: true
  phpdoc_no_useless_inheritdoc: true
  phpdoc_order: true
  phpdoc_return_self_reference: true
  phpdoc_scalar: true
  phpdoc_separation: true
  phpdoc_single_line_var_spacing: true
  phpdoc_trim: true
  phpdoc_trim_consecutive_blank_line_separation: true
  phpdoc_types: true
  phpdoc_var_without_name: true
  psr4: true
  random_api_migration: true
  return_assignment: true
  return_type_declaration: true
  self_accessor: true
  semicolon_after_instruction: true
  set_type_to_cast: true
  short_scalar_cast: true
  simplified_null_return: true
  single_blank_line_before_namespace: true
  single_line_comment_style: true
  single_quote: true
  space_after_semicolon:
    remove_in_empty_for_expressions: true
  standardize_increment: true
  standardize_not_equals: true
  strict_param: true
  ternary_operator_spaces: true
  ternary_to_null_coalescing: true
  trailing_comma_in_multiline_array: true
  trim_array_spaces: true
  unary_operator_spaces: true
  whitespace_after_comma_in_array: true
  yoda_style: true
risky: true

*/
