<?php

return [

    // 1. Pedroo Normalize Service
    'normalize' => [
        'title' => 'Pedroo Normalize Service',
        'tasks' => [
            // ? K?sz
            'ring_normalization' => true,
            'class_normalization' => true,
            'qualification_normalization' => true,
            'judge_normalization' => true,
            'country_code_basic' => true,
            'reg_no_basic' => true,
            'content_based_builder' => true,
            'multilingual_ui_fields' => true,
            'name_resolution_advanced' => true,
            'kennel_name_detector' => true,
            'reg_no_country_detector_basic' => true,
            'parent_normalizer' => true,
            'parent_matching_service' => true,
            'father_mother_fields' => true,
            'reg_no_rules_by_country' => true,
            'color_normalization' => true,

            // ? H?tral?vo
            'diagnosis_normalization' => false,
            'country_code_100' => false,
            'normalize_api_endpoint' => false,
            'normalize_unit_tests' => false,
        ],
    ],

    // 2. Pedroo Ingest Service
    'ingest' => [
        'title' => 'Pedroo Ingest Service',
        'tasks' => [
            // ? K?sz
            'content_based_mapping' => true,
            'sandbox_to_final_pipeline' => true,
            'normalizer_integration' => true,
            'multilingual_input_support' => true,

            // ? H?tral?vo
            'excel_import_ui' => false,
            'csv_import_ui' => false,
            'json_import_ui' => false,
            'api_ingest_endpoints' => false,
            'web_scraping_modules' => false,
            'ingest_log_system' => false,
            'ingest_error_handling' => false,
            'ingest_unit_tests' => false,
        ],
    ],

    // 3. Kutya adatb?zis
    'dog_database' => [
        'title' => 'Kutya adatb?zis',
        'tasks' => [
            // ? K?sz
            'data_model' => true,
            'sandbox_to_final_pipeline' => true,
            'normalizers_integrated' => true,
            'dog_normalizer_final' => true,
            'dog_table_extended' => true,
            'father_mother_fields_added' => true,

            // ? H?tral?vo
            'excel_import' => false,
            'reg_no_advanced' => false,
            'country_code_100' => false,
            'duplicate_detection' => false,
            'dog_merge_ui' => false,
            'dog_audit_module' => false,
        ],
    ],

    // 4. Audit rendszer
    'audit' => [
        'title' => 'Audit rendszer',
        'tasks' => [
            // ? K?sz
            'audit_pipeline_base' => true,
            'audit_log_structure' => true,

            // ? H?tral?vo
            'audit_ui' => false,
            'automatic_error_detection' => false,
            'fix_suggestions' => false,
            'audit_fix_pipeline' => false,
            'audit_export' => false,
            'audit_unit_tests' => false,
        ],
    ],

    // 5. Esem?ny modul
    'events' => [
        'title' => 'Esem?ny modul',
        'tasks' => [
            // ? K?sz
            'ring_normalization' => true,
            'class_normalization' => true,
            'judge_normalization' => true,
            'qualification_normalization' => true,
            'multilingual_ui_fields' => true,
            'event_metadata_structure' => true,

            // ? H?tral?vo
            'event_metadata_normalization' => false,
            'event_ingest_pipeline' => false,
            'show_calendar_integration' => false,
            'eventbrite_provider' => false,
            'google_events_provider' => false,
            'event_list_ui' => false,
            'event_details_ui' => false,
            'event_results_linking' => false,
        ],
    ],

    // 6. Alomtervez?s
    'breeding' => [
        'title' => 'Alomtervez?s',
        'tasks' => [
            // ? K?sz
            'coi_base_logic' => true,
            'genetics_module_structure' => true,

            // ? H?tral?vo
            'coi_engine' => false,
            'color_genetics_engine' => false,
            'health_risk_estimation' => false,
            'breeding_plan_generation' => false,
            'breeding_plan_ui' => false,
            'genetics_module_ui' => false,
        ],
    ],

    // 7. T?bbnyelvu UI
    'i18n' => [
        'title' => 'T?bbnyelvu UI',
        'tasks' => [
            // ? K?sz
            'multilingual_field_logic' => true,
            'language_file_structure' => true,

            // ? H?tral?vo
            'full_ui_key_list' => false,
            'hu_language_file' => false,
            'en_language_file' => false,
            'de_language_file' => false,
            'ro_language_file' => false,
            'es_language_file' => false,
            'pt_language_file' => false,
            'language_switcher_ui' => false,
            'language_auto_detection' => false,
        ],
    ],

    // 8. Final Pipeline
    'final_pipeline' => [
        'title' => 'Final Pipeline',
        'tasks' => [
            // ? K?sz
            'sandbox_to_final_logic' => true,
            'normalizers_integrated' => true,
            'parent_matching_integrated' => true,

            // ? H?tral?vo
            'promotion_rules' => false,
            'promotion_error_handling' => false,
            'promotion_log' => false,
            'promotion_rollback' => false,
            'promotion_unit_tests' => false,
        ],
    ],

    // 9. Kennel aktivit?s
    'kennel_activity' => [
        'title' => 'Kennel aktivit?s',
        'tasks' => [
            'yearly_activity_check' => false,
            'active_status_update' => false,
            'kennel_audit_report' => false,
            'kennel_activity_ui' => false,
        ],
    ],

    // 10. Find a Club Near Me
    'club_finder' => [
        'title' => 'Find a Club Near Me',
        'tasks' => [
            'agility' => false,
            'dog_showing' => false,
            'breed_clubs' => false,
            'field_trials' => false,
            'good_citizen_scheme' => false,
            'gundog_working_test' => false,
            'heelwork_to_music' => false,
            'obedience' => false,
            'rally' => false,
            'working_trial' => false,
            'more_activities' => false,
            'club_database' => false,
            'club_search_ui' => false,
            'location_based_search' => false,
        ],
    ],

    // 11. Pedigree PDF Creator
    'pedigree_pdf' => [
        'title' => 'Pedigree PDF Creator',
        'tasks' => [
            'layout_design' => false,
            'pdf_3gen' => false,
            'pdf_4gen' => false,
            'color_bw_modes' => false,
            'export_button' => false,
            'pdf_engine_integration' => false,
        ],
    ],

    // 12. Find a Doctor Near Me
    'doctor_finder' => [
        'title' => 'Find a Doctor Near Me',
        'tasks' => [
            'vet_database' => false,
            'clinic_database' => false,
            'specialist_categories' => false,
            'location_based_search' => false,
            'distance_sorting' => false,
            'service_filters' => false,
            'opening_hours_filter' => false,
            'ratings' => false,
            'doctor_profile' => false,
            'clinic_profile' => false,
            'call_and_navigation_buttons' => false,
        ],
    ],


];