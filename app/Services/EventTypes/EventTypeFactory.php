<?php

namespace App\Services\EventTypes;

use Exception;

class EventTypeFactory
{
    protected static array $map = [

        // -------------------------------------------------
        // SHOW CATEGORY
        // -------------------------------------------------
        'show.cac'            => \App\Services\EventTypes\Show\CACModule::class,
        'show.cacib'          => \App\Services\EventTypes\Show\CACIBModule::class,
        'show.club'           => \App\Services\EventTypes\Show\ClubShowModule::class,
        'show.specialty'      => \App\Services\EventTypes\Show\SpecialtyShowModule::class,
        'show.champion'       => \App\Services\EventTypes\Show\ChampionShowModule::class,
        'show.puppy'          => \App\Services\EventTypes\Show\PuppyShowModule::class,
        'show.veteran'        => \App\Services\EventTypes\Show\VeteranShowModule::class,

        // -------------------------------------------------
        // SPORT CATEGORY
        // -------------------------------------------------
        'sport.agility'       => \App\Services\EventTypes\Sport\AgilityModule::class,
        'sport.igp'           => \App\Services\EventTypes\Sport\IGPModule::class,
        'sport.obedience'     => \App\Services\EventTypes\Sport\ObedienceModule::class,
        'sport.rally'         => \App\Services\EventTypes\Sport\RallyModule::class,
        'sport.coursing'      => \App\Services\EventTypes\Sport\CoursingModule::class,
        'sport.flyball'       => \App\Services\EventTypes\Sport\FlyballModule::class,
        'sport.dock_diving'   => \App\Services\EventTypes\Sport\DockDivingModule::class,
        'sport.herding'       => \App\Services\EventTypes\Sport\HerdingModule::class,
        'sport.field_trial'   => \App\Services\EventTypes\Sport\FieldTrialModule::class,
        'sport.nosework'      => \App\Services\EventTypes\Sport\NoseworkModule::class,
        'sport.working_test'  => \App\Services\EventTypes\Sport\WorkingTestModule::class,
        'sport.water_test'    => \App\Services\EventTypes\Sport\WaterTestModule::class,
        'sport.weight_pulling'=> \App\Services\EventTypes\Sport\WeightPullingModule::class,
        'sport.treibball'     => \App\Services\EventTypes\Sport\TreibballModule::class,
        'sport.hoopers'       => \App\Services\EventTypes\Sport\HoopersModule::class,
        'sport.mondioring'    => \App\Services\EventTypes\Sport\MondioringModule::class,
        'sport.schutzhund'    => \App\Services\EventTypes\Sport\SchutzhundModule::class,

        // -------------------------------------------------
        // HEALTH CATEGORY
        // -------------------------------------------------
        'health.hd'           => \App\Services\EventTypes\Health\HDScreeningModule::class,
        'health.ed'           => \App\Services\EventTypes\Health\EDScreeningModule::class,
        'health.genetic'      => \App\Services\EventTypes\Health\GeneticTestModule::class,
        'health.screening'    => \App\Services\EventTypes\Health\HealthScreeningModule::class,
        'health.microchip'    => \App\Services\EventTypes\Health\MicrochippingModule::class,
        'health.vaccination'  => \App\Services\EventTypes\Health\VaccinationModule::class,
        'health.surgery'      => \App\Services\EventTypes\Health\SurgeryModule::class,
        'health.physiotherapy'=> \App\Services\EventTypes\Health\PhysiotherapyModule::class,
        'health.rehab'        => \App\Services\EventTypes\Health\RehabilitationModule::class,

        // -------------------------------------------------
        // BREEDING CATEGORY
        // -------------------------------------------------
        'breeding.mating'     => \App\Services\EventTypes\Breeding\MatingModule::class,
        'breeding.birth'      => \App\Services\EventTypes\Breeding\BirthModule::class,
        'breeding.litter_check'=> \App\Services\EventTypes\Breeding\LitterCheckModule::class,
        'breeding.exam'       => \App\Services\EventTypes\Breeding\BreedingExamModule::class,
        'breeding.license'    => \App\Services\EventTypes\Breeding\BreedingLicenseModule::class,
        'breeding.parentage'  => \App\Services\EventTypes\Breeding\ParentageVerificationModule::class,

        // -------------------------------------------------
        // COMMUNITY CATEGORY
        // -------------------------------------------------
        'community.dog_meetup'          => \App\Services\EventTypes\Community\DogMeetupModule::class,
        'community.breed_meetup'        => \App\Services\EventTypes\Community\BreedMeetupModule::class,
        'community.dog_festival'        => \App\Services\EventTypes\Community\DogFestivalModule::class,
        'community.dog_workshop'        => \App\Services\EventTypes\Community\DogWorkshopModule::class,
        'community.dog_photoshoot'      => \App\Services\EventTypes\Community\DogPhotoshootModule::class,
        'community.dog_picnic'          => \App\Services\EventTypes\Community\DogPicnicModule::class,
        'community.adoption_day'        => \App\Services\EventTypes\Community\AdoptionDayModule::class,
        'community.training_class'      => \App\Services\EventTypes\Community\TrainingClassModule::class,
        'community.training_log'        => \App\Services\EventTypes\Community\TrainingLogModule::class,
        'community.online_competition'  => \App\Services\EventTypes\Community\OnlineCompetitionModule::class,
        'community.online_dog_show'     => \App\Services\EventTypes\Community\OnlineDogShowModule::class,
        'community.online_photo_contest'=> \App\Services\EventTypes\Community\OnlinePhotoContestModule::class,
        'community.online_trick_contest'=> \App\Services\EventTypes\Community\OnlineTrickContestModule::class,
        'community.virtual_run'         => \App\Services\EventTypes\Community\VirtualRunModule::class,
        'community.virtual_show'        => \App\Services\EventTypes\Community\VirtualShowModule::class,
    ];

    public static function make(string $type): BaseEventType
    {
        if (!isset(self::$map[$type])) {
            throw new Exception("Unknown event type: {$type}");
        }

        $class = self::$map[$type];
        return new $class();
    }

    public static function allTypes(): array
    {
        return array_keys(self::$map);
    }

    public static function metadata(): array
    {
        $result = [];
        foreach (self::$map as $type => $class) {
            /** @var BaseEventType $class */
            $result[$type] = $class::metadata();
        }
        return $result;
    }
}