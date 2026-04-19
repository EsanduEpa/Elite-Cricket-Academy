<?php

/**
 * Public home page controller.
 *
 * This controller does not require login. It collects public-facing data
 * from several models and sends one combined $data array to app/views/v_home.php.
 */
class Home extends Controller {
    private $eventModel;
    private $userModel;
    private $shopModel;
    private $feedbackModel;
    
    public function __construct() {
        // The home page is a summary page, so it needs data from events,
        // coaches/users, facilities/shop, and feedback/testimonials.
        $this->eventModel = $this->model('Event');
        $this->userModel = $this->model('M_Users');
        $this->shopModel = $this->model('M_Shop');
        $this->feedbackModel = $this->model('Feedback');
    }

    public function index() {
        // Build the complete page data in the controller so the view can focus on HTML.
        $data = [
            'title' => 'Elite Cricket Academy',
            'full_width_footer' => true,
            'programs' => $this->getHomePrograms(),
            'coaches' => array_slice($this->userModel->getAllCoachProfiles(), 0, 6),
            'facilities' => array_slice($this->shopModel->getAllFacilities(), 0, 6),
            'events' => array_slice($this->eventModel->getUpcomingEvents(3), 0, 3),
            'testimonials' => $this->getHomeTestimonials(),
            'contact' => $this->getHomeContactDetails(),
            'homeStats' => $this->getHomeStats()
        ];

        $this->view('v_home', $data);
    }

    private function getHomePrograms(): array {
        // Public programs are generated from active membership plans in the database.
        $plans = $this->userModel->getActiveMembershipPlans();
        $programs = [];

        foreach ($plans as $index => $plan) {
            $description = trim((string)($plan->Description ?? ''));
            $programs[] = [
                'name' => ucfirst((string)$plan->PlanName) . ' Program',
                'description' => $description !== '' ? $description : 'Structured cricket development program.',
                'monthly_fee' => (float)($plan->MonthlyFee ?? 0),
                'sessions_per_week' => (int)($plan->SessionsPerWeek ?? 0),
                'private_sessions' => (int)($plan->PrivateSessionsIncluded ?? 0),
                'facility_access' => !empty($plan->FacilityAccessIncluded),
                'image_class' => $this->getProgramImageClass($index)
            ];
        }

        return $programs;
    }

    private function getProgramImageClass(int $index): string {
        $classes = ['youth-program', 'advanced-training', 'coaching-camps'];
        return $classes[$index % count($classes)];
    }

    private function getHomeTestimonials(): array {
        // Only show feedback that has both a rating and a message.
        $feedback = $this->feedbackModel->getAllFeedbacks();
        $testimonials = [];

        foreach ($feedback as $item) {
            $rating = (int)($item['rating'] ?? 0);
            $message = trim((string)($item['message'] ?? ''));
            if ($rating <= 0 || $message === '') {
                continue;
            }

            $testimonials[] = [
                'name' => (string)($item['user_name'] ?? 'Academy Member'),
                'category' => ucfirst((string)($item['subject'] ?? 'general')),
                'date' => (string)($item['created_at'] ?? ''),
                'rating' => max(1, min(5, $rating)),
                'text' => $message
            ];

            if (count($testimonials) === 3) {
                break;
            }
        }

        return $testimonials;
    }

    private function getHomeContactDetails(): array {
        // Contact details are inferred from upcoming events first, then facilities.
        // This avoids hardcoding contact information on the home page.
        $events = $this->eventModel->getUpcomingEvents(5);
        $contact = [
            'address_lines' => [],
            'phones' => [],
            'emails' => [],
            'hours' => 'Check upcoming sessions and event schedules for the latest operating times.'
        ];

        foreach ($events as $event) {
            $location = trim((string)($event['location'] ?? ''));
            $phone = trim((string)($event['ContactPhone'] ?? ''));
            $email = trim((string)($event['ContactEmail'] ?? ''));

            if ($location !== '' && !in_array($location, $contact['address_lines'], true)) {
                $contact['address_lines'][] = $location;
            }

            if ($phone !== '' && !in_array($phone, $contact['phones'], true)) {
                $contact['phones'][] = $phone;
            }

            if ($email !== '' && !in_array($email, $contact['emails'], true)) {
                $contact['emails'][] = $email;
            }
        }

        if (empty($contact['address_lines'])) {
            $facilities = array_slice($this->shopModel->getAllFacilities(), 0, 3);
            foreach ($facilities as $facility) {
                $location = trim((string)($facility->Location ?? ''));
                if ($location !== '' && !in_array($location, $contact['address_lines'], true)) {
                    $contact['address_lines'][] = $location;
                }
            }
        }

        return $contact;
    }

    private function getHomeStats(): array {
        // Small counters used by the landing page statistics section.
        return [
            'program_count' => count($this->userModel->getActiveMembershipPlans()),
            'coach_count' => count($this->userModel->getAllCoachProfiles()),
            'facility_count' => count($this->shopModel->getAllFacilities()),
            'upcoming_event_count' => count($this->eventModel->getUpcomingEvents(10))
        ];
    }
}
