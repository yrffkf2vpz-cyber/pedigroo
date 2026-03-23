<?php

return [

    'files' => [

    // ROOT
    'artisan',
    'composer.json',
    'package.json',

    // ROUTES
    'routes/web.php',
    'routes/api.php',
    'routes/console.php',
    'routes/auth.php',

    // CONSOLE
    'app/Console/Kernel.php',
    'app/Console/Commands/CreateRemoteFile.php',
    'app/Console/Commands/DispatchNormalizeDogs.php',
    'app/Console/Commands/ImportKuvaszHealthCommand.php',
    'app/Console/Commands/ImportSingleKuvaszCommand.php',
    'app/Console/Commands/IngestEvents.php',
    'app/Console/Commands/NormalizeDogsCommand.php',
    'app/Console/Commands/NormalizeStatsCommand.php',
    'app/Console/Commands/ParseEventCommand.php',
    'app/Console/Commands/PedrooAudit.php',
    'app/Console/Commands/PedrooDiagnose.php',
    'app/Console/Commands/PedrooFetchOneDog.php',
    'app/Console/Commands/PedrooIngestCommand.php',
    'app/Console/Commands/PedrooPlanCommand.php',
    'app/Console/Commands/PedrooRunCommand.php',
    'app/Console/Commands/PedrooRunV3Command.php',
    'app/Console/Commands/PedrooScanDatabase.php',
    'app/Console/Commands/PedrooScanFilesystem.php',
    'app/Console/Commands/PedrooScanPipeline.php',
    'app/Console/Commands/PromoteAllCommand.php',
    'app/Console/Commands/PromoteResultsCommand.php',
    'app/Console/Commands/RetryFailedNormalizeDogs.php',
    'app/Console/Commands/ScaffoldPipelineTasks.php',
    'app/Console/Commands/WriteRemoteFile.php',

    // CONTROLLERS
    'app/Http/Controllers/Controller.php',
    'app/Http/Controllers/ActivationController.php',
    'app/Http/Controllers/AgentController.php',
    'app/Http/Controllers/AiController.php',
    'app/Http/Controllers/ConsoleController.php',
    'app/Http/Controllers/DogController.php',
    'app/Http/Controllers/Dog/DogProfileController.php',
    'app/Http/Controllers/Dog/DogChampionshipController.php',
    'app/Http/Controllers/FileManagerController.php',
    'app/Http/Controllers/IngestController.php',
    'app/Http/Controllers/LearningQueueController.php',
    'app/Http/Controllers/NormalizeController.php',
    'app/Http/Controllers/PedrooCopilotController.php',
    'app/Http/Controllers/PipelineController.php',
    'app/Http/Controllers/PipelineDashboardController.php',
    'app/Http/Controllers/ProfileController.php',

    // Dev controllers
    'app/Http/Controllers/Dev/DevCodePatchController.php',
    'app/Http/Controllers/Dev/DevDatabaseIntrospectController.php',
    'app/Http/Controllers/Dev/DevFileSystemController.php',
    'app/Http/Controllers/Dev/DevModuleGeneratorController.php',
    'app/Http/Controllers/Dev/FsController.php',

    // Auth controllers
    'app/Http/Controllers/Auth/AuthenticatedSessionController.php',
    'app/Http/Controllers/Auth/ConfirmablePasswordController.php',
    'app/Http/Controllers/Auth/EmailVerificationNotificationController.php',
    'app/Http/Controllers/Auth/EmailVerificationPromptController.php',
    'app/Http/Controllers/Auth/NewPasswordController.php',
    'app/Http/Controllers/Auth/PasswordController.php',
    'app/Http/Controllers/Auth/PasswordResetLinkController.php',
    'app/Http/Controllers/Auth/RegisteredUserController.php',
    'app/Http/Controllers/Auth/VerifyEmailController.php',

    // MODELS
    'app/Models/User.php',
    'app/Models/Dog.php',
    'app/Models/Country.php',
    'app/Models/HealthAlias.php',
    'app/Models/HealthRecord.php',
    'app/Models/PipelineTask.php',
    'app/User.php',

    // DTO
    'app/Dto/HealthRecord.php',
    'app/Dto/RawDogData.php',

    // PROVIDERS
    'app/Providers/AppServiceProvider.php',
    'app/Providers/AuthServiceProvider.php',
    'app/Providers/EventServiceProvider.php',
    'app/Providers/RouteServiceProvider.php',

    // SERVICES (?sszes domain)
    // Activation
    'app/Services/Activation/DogActivationService.php',

    // Agent
    'app/Services/Agent/PedrooAgentService.php',
    'app/Services/Agent/PedrooAgentValidatorService.php',

    // Diagnosis
    'app/Services/Diagnosis/DiagnosisNormalizer.php',

    // Dog
    'app/Services/Dog/DogActivationService.php',
    'app/Services/Dog/DogChampionshipService.php',
    'app/Services/Dog/DogFinder.php',
    'app/Services/Dog/DogProfileService.php',
    'app/Services/Dog/KuvaszAdatbazisDogFinder.php',

    // Events
    'app/Services/Events/EventNormalizerService.php',
    'app/Services/Events/EventParserService.php',
    'app/Services/Events/EventsIngestService.php',

    // FileManager
    'app/Services/FileManager/FileManagerService.php',

    // Health
    'app/Services/Health/HealthImportService.php',
    'app/Services/Health/HealthNormalizer.php',
    'app/Services/Health/KuvaszAdatbazisHealthNormalizer.php',

    // History
    'app/Services/History/HistoryIngestService.php',

    // Import
    'app/Services/Import/YourImportService.php',

    // Ingest
    'app/Services/Ingest/DogRecordBuilder.php',
    'app/Services/Ingest/DogRecordSaver.php',
    'app/Services/Ingest/ExcelDogRowBuilder.php',
    'app/Services/Ingest/IngestApiService.php',
    'app/Services/Ingest/IngestExcelService.php',
    'app/Services/Ingest/IngestPdfService.php',
    'app/Services/Ingest/IngestScraperService.php',
    'app/Services/Ingest/IngestService.php',
    'app/Services/Ingest/PedrooHealthRecordSaver.php',
    'app/Services/Ingest/SandboxDogSaver.php',

    // Normalizers (?sszes)
    'app/Services/Normalizers/AdvancedNameParser.php',
    'app/Services/Normalizers/BreedNormalizer.php',
    'app/Services/Normalizers/ChampionPointCalculator.php',
    'app/Services/Normalizers/ChampionshipNormalizer.php',
    'app/Services/Normalizers/ChangeDetector.php',
    'app/Services/Normalizers/ClassNormalizer.php',
    'app/Services/Normalizers/ClassPromotionNormalizer.php',
    'app/Services/Normalizers/ColorNormalizer.php',
    'app/Services/Normalizers/CountryDetector.php',
    'app/Services/Normalizers/CountryNormalizer.php',
    'app/Services/Normalizers/DogPromotionNormalizer.php',
    'app/Services/Normalizers/DuplicateResolver.php',
    'app/Services/Normalizers/EventNormalizer.php',
    'app/Services/Normalizers/EventPromotionNormalizer.php',
    'app/Services/Normalizers/HistoryWriter.php',
    'app/Services/Normalizers/Jobs/NormalizeDogJob.php',
    'app/Services/Normalizers/JudgeNormalizer.php',
    'app/Services/Normalizers/JudgePromotionNormalizer.php',
    'app/Services/Normalizers/KennelNameDetector.php',
    'app/Services/Normalizers/LocationNormalizer.php',
    'app/Services/Normalizers/Name/NameParser.php',
    'app/Services/Normalizers/NormalizeBreederService.php',
    'app/Services/Normalizers/NormalizeBreedService.php',
    'app/Services/Normalizers/NormalizeColorService.php',
    'app/Services/Normalizers/NormalizeCountryService.php',
    'app/Services/Normalizers/NormalizeDogService.php',
    'app/Services/Normalizers/NormalizeHealthService.php',
    'app/Services/Normalizers/NormalizeKennelService.php',
    'app/Services/Normalizers/NormalizeOwnerService.php',
    'app/Services/Normalizers/NormalizeParentService.php',
    'app/Services/Normalizers/NormalizePipelineService.php',
    'app/Services/Normalizers/NormalizeResultsService.php',
    'app/Services/Normalizers/NormalizeService.php',
    'app/Services/Normalizers/ParentMatchingService.php',
    'app/Services/Normalizers/PlacementPromotionNormalizer.php',
    'app/Services/Normalizers/PromotionNormalizer.php',
    'app/Services/Normalizers/QualificationPromotionNormalizer.php',
    'app/Services/Normalizers/RegNo/BreedCodeDetector.php',
    'app/Services/Normalizers/RegNo/CountryPatterns.php',
    'app/Services/Normalizers/RegNo/CountryRepository.php',
    'app/Services/Normalizers/RegNo/OrganizationDetector.php',
    'app/Services/Normalizers/RegNo/RegNoCountryDetector.php',
    'app/Services/Normalizers/RegNo/RegNoMotor.php',
    'app/Services/Normalizers/RegNo/RegNoNormalizer.php',
    'app/Services/Normalizers/RegNo/RegNoParser.php',
    'app/Services/Normalizers/RegNo/RegNoResult.php',
    'app/Services/Normalizers/RegNo/RegNoService.php',
    'app/Services/Normalizers/RegNo/SequenceDetector.php',
    'app/Services/Normalizers/RegNo/StatusDetector.php',
    'app/Services/Normalizers/RegNo/YearDetector.php',
    'app/Services/Normalizers/RingNormalizer.php',
    'app/Services/Normalizers/RingPromotionNormalizer.php',
    'app/Services/Normalizers/Rules/CleanupRules.php',
    'app/Services/Normalizers/Rules/KennelRules.php',
    'app/Services/Normalizers/Rules/OwnerRules.php',
    'app/Services/Normalizers/Rules/PrefixRules.php',
    'app/Services/Normalizers/Rules/RegNoRules.php',
    'app/Services/Normalizers/Rules/SuffixRules.php',
    'app/Services/Normalizers/TitleNormalizer.php',

    // Parsers
    'app/Services/Parsers/CsvResultParser.php',
    'app/Services/Parsers/GenericPdfParser.php',
    'app/Services/Parsers/MkszHtmlParser.php',

    // Pedroo core
    'app/Services/Pedroo/BreederService.php',
    'app/Services/Pedroo/DogActivationService.php',
    'app/Services/Pedroo/DogResultService.php',
    'app/Services/Pedroo/EventService.php',
    'app/Services/Pedroo/KennelService.php',
    'app/Services/Pedroo/OwnerService.php',
    'app/Services/Pedroo/ParentService.php',
    'app/Services/Pedroo/PedrooCodeGenerator.php',
    'app/Services/Pedroo/PedrooEngine.php',
    'app/Services/Pedroo/PedrooTaskParserService.php',
    'app/Services/Pedroo/ShowResultService.php',
    'app/Services/Pedroo/ShowService.php',
    'app/Services/PedrooDogFetcher.php',

    // Pipeline
    'app/Services/Pipeline/PipelineService.php',
    'app/Services/Pipeline/PipelineTaskGenerator.php',

    // Promotion
    'app/Services/Promotion/BreederPromotionService.php',
    'app/Services/Promotion/ChampionshipImportService.php',
    'app/Services/Promotion/ChampionshipPromotionService.php',
    'app/Services/Promotion/EventPromotionService.php',
    'app/Services/Promotion/HealthPromotionService.php',
    'app/Services/Promotion/HistoryPromotionService.php',
    'app/Services/Promotion/KennelPromotionService.php',
    'app/Services/Promotion/OwnerPromotionService.php',
    'app/Services/Promotion/ParentPromotionService.php',
    'app/Services/Promotion/PromotionRunner.php',
    'app/Services/Promotion/ResultPromotionService.php',
    'app/Services/Promotion/TitlePromotionService.php',

    // Title
    'app/Services/Title/TitleDefinitionService.php',
    'app/Services/Title/TitleImportService.php',
    'app/Services/Title/TitleNormalizer.php',

    // Misc
    'app/Services/AIAliasGeneratorService.php',
    'app/Services/AISuggestionService.php',
    'app/Services/FuzzyMatchService.php',

    // TASKS
    'app/Tasks/AuditErrorDetection.php',
    'app/Tasks/AuditExport.php',
    'app/Tasks/AuditFixPipeline.php',
    'app/Tasks/AuditFixSuggestions.php',
    'app/Tasks/AuditTests.php',
    'app/Tasks/AuditUi.php',
    'app/Tasks/System/ScaffoldMissingTasks.php',

    // VIEWS
    'resources/views/welcome.blade.php',
    'resources/views/dashboard.blade.php',
    'resources/views/dashboard/pipeline-status.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/layouts/guest.blade.php',
    'resources/views/layouts/navigation.blade.php',
    'resources/views/pedroo/console.blade.php',
    'resources/views/pedroo/dashboard/pipeline-status.blade.php',
    'resources/views/pedroo_copilot.blade..php',

    // CONFIG
    'config/app.php',
    'config/auth.php',
    'config/cache.php',
    'config/database.php',
    'config/filesystems.php',
    'config/logging.php',
    'config/mail.php',
    'config/queue.php',
    'config/services.php',
    'config/session.php',
    'config/breed_rules.php',
    'config/country_rules.php',
    'config/dog_colors.php',
    'config/fieldname_country_patterns.php',
    'config/health_rules.php',
    'config/ingest_rules.php',
    'config/kennel_country_patterns.php',
    'config/normalize.php',
    'config/pedroo_plan.php',
    'config/permission.php',
    'config/pipeline.php',
    'config/regno_rules.php',

    // MIGRATIONS
    'database/migrations/0001_01_01_000000_create_users_table.php',
    'database/migrations/0001_01_01_000001_create_cache_table.php',
    'database/migrations/0001_01_01_000002_create_jobs_table.php',
    'database/migrations/2026_02_21_000100_create_pipeline_tasks_table.php',
    'database/migrations/2026_02_23_153000_create_pd_pedroo_registry_table.php',

    // PUBLIC
    'public/index.php',
    'public/.htaccess',
    'public/favicon.ico',
    'public/robots.txt',
    'public/build/manifest.json',
    'public/build/assets/app-CBbTb_k3.js',
    'public/build/assets/app-CWKJCBZz.css',
],


];

