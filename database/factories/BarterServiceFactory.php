<?php

namespace Database\Factories;

use App\Models\BarterCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BarterService>
 */
class BarterServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeThisMonth();

        $min_price = fake()->randomFloat(2, 0, 99);
        $max_price = fake()->randomFloat(2, $min_price, 99);
        $status = [
            'enabled',
            'disabled',
        ];

        $category_details = [
            'Home Cleaning' => [
                [
                    'title' => 'Deep House Cleaning Specialist',
                    'description' => 'Comprehensive deep cleaning service covering every corner of your home. Includes thorough dusting, vacuuming, floor scrubbing, and detailed cleaning of kitchens and bathrooms. Using professional-grade equipment and eco-friendly cleaning solutions.'
                ],
                [
                    'title' => 'Weekly Maintenance Cleaning Service',
                    'description' => 'Regular weekly cleaning service to keep your home consistently clean. Includes general dusting, vacuuming, bathroom cleaning, kitchen maintenance, and floor care. Perfect for busy families needing reliable household maintenance.'
                ],
                [
                    'title' => 'Move-In/Move-Out Cleaning Expert',
                    'description' => 'Specialized cleaning service for property transitions. Includes deep cleaning of all surfaces, inside cabinets and appliances, window cleaning, and carpet cleaning. Ensuring properties are spotless for new occupants.'
                ],
                [
                    'title' => 'Green Cleaning Solutions Provider',
                    'description' => 'Environmentally conscious cleaning service using only natural, non-toxic products. Perfect for families with children, pets, or chemical sensitivities. Includes all standard cleaning tasks with eco-friendly alternatives.'
                ],
                [
                    'title' => 'Premium House Sanitization Service',
                    'description' => 'Professional sanitization and disinfection service using hospital-grade products. Focuses on eliminating germs, bacteria, and allergens throughout your home. Includes detailed cleaning of high-touch surfaces and air quality improvement.'
                ]
            ],
            'Plumbing Services' => [
                [
                    'title' => '24/7 Emergency Plumber',
                    'description' => 'Round-the-clock emergency plumbing service for urgent issues. Specialized in rapid response to water leaks, pipe bursts, blocked drains, and flooding problems. Available 24/7 with quick response times.'
                ],
                [
                    'title' => 'Bathroom Renovation Plumber',
                    'description' => 'Expert bathroom plumbing installation and renovation services. Including fixture installation, pipe relocation, shower/bath plumbing, and complete bathroom remodeling plumbing support. Licensed and insured professional.'
                ],
                [
                    'title' => 'Drain Cleaning Specialist',
                    'description' => 'Professional drain and sewer cleaning services using advanced equipment. Includes camera inspection, hydro jetting, root removal, and preventive maintenance for all types of drains and pipes.'
                ],
                [
                    'title' => 'Water Heater Installation Expert',
                    'description' => 'Specialized in installing and maintaining all types of water heaters. Services include traditional, tankless, and solar water heater installation, repairs, and maintenance. Energy efficiency consulting included.'
                ],
                [
                    'title' => 'Leak Detection Professional',
                    'description' => 'Advanced leak detection and repair services using latest technology. Includes non-invasive detection methods, pressure testing, and precise repair solutions for both visible and hidden leaks.'
                ]
            ],
            'Electrical Repairs' => [
                [
                    'title' => 'Residential Electrical Specialist',
                    'description' => 'Comprehensive residential electrical services including wiring repairs, outlet installation, circuit breaker updates, and safety inspections. Licensed electrician ensuring code compliance and safety standards.'
                ],
                [
                    'title' => 'Smart Home Installation Expert',
                    'description' => 'Professional installation of smart home electrical systems. Services include smart lighting, thermostat installation, security system wiring, and home automation integration. Certified in latest smart home technologies.'
                ],
                [
                    'title' => 'Emergency Electrical Repairs',
                    'description' => 'Rapid response electrical repair service for urgent issues. Available for power outages, electrical failures, circuit overloads, and safety hazards. 24/7 emergency service with quick response times.'
                ],
                [
                    'title' => 'Lighting Installation Professional',
                    'description' => 'Expert in all types of lighting installation and repairs. Services include indoor and outdoor lighting, LED upgrades, fixture installation, and lighting design consultation. Energy-efficient solutions available.'
                ],
                [
                    'title' => 'Electrical Panel Upgrade Specialist',
                    'description' => 'Specialized in electrical panel upgrades and maintenance. Including panel replacement, circuit addition, amp service upgrades, and complete electrical system assessments. Licensed for all electrical panel work.'
                ]
            ],
            'Gardening & Landscaping' => [
                [
                    'title' => 'Professional Garden Designer',
                    'description' => 'Creative garden design and implementation service. Includes landscape planning, plant selection, garden layout design, and complete installation. Specializing in sustainable and low-maintenance gardens.'
                ],
                [
                    'title' => 'Lawn Care Specialist',
                    'description' => 'Complete lawn maintenance and care service. Including mowing, edging, fertilization, aeration, and pest control. Regular maintenance schedules available for year-round lawn health.'
                ],
                [
                    'title' => 'Tree Care Expert',
                    'description' => 'Professional tree maintenance and care services. Offering pruning, trimming, disease treatment, and removal services. Certified arborist ensuring proper tree health and safety.'
                ],
                [
                    'title' => 'Irrigation System Professional',
                    'description' => 'Specialized in irrigation system installation and maintenance. Services include sprinkler installation, drip system setup, timer programming, and system repairs. Water-efficient solutions available.'
                ],
                [
                    'title' => 'Hardscape Installation Specialist',
                    'description' => 'Expert installation of garden hardscape features. Including patios, walkways, retaining walls, and water features. Custom design and professional installation of all hardscape elements.'
                ]
            ],
            'Personal Training' => [
                [
                    'title' => 'Certified Personal Fitness Trainer',
                    'description' => 'Personalized fitness training programs tailored to your goals. Includes customized workout plans, nutritional guidance, and progress tracking. One-on-one sessions focused on achieving optimal results safely.'
                ],
                [
                    'title' => 'Weight Loss Specialist',
                    'description' => 'Dedicated weight loss training and coaching program. Including customized exercise routines, dietary planning, and lifestyle modification strategies. Proven methods for sustainable weight loss results.'
                ],
                [
                    'title' => 'Senior Fitness Coach',
                    'description' => 'Specialized fitness training for seniors and older adults. Focusing on strength, balance, flexibility, and mobility. Safe, low-impact exercises designed for senior health and wellness.'
                ],
                [
                    'title' => 'Sports Performance Trainer',
                    'description' => 'Advanced athletic training for sports performance enhancement. Including sport-specific conditioning, agility training, and performance optimization. Suitable for athletes of all levels.'
                ],
                [
                    'title' => 'Post-Rehabilitation Fitness Expert',
                    'description' => 'Specialized training for post-injury recovery and rehabilitation. Working in conjunction with medical professionals to ensure safe return to activity. Customized programs for various recovery needs.'
                ]
            ],
            'Graphic Design' => [
                [
                    'title' => 'Brand Identity Designer',
                    'description' => 'Complete brand identity design service. Creating logos, color schemes, typography selections, and brand guidelines. Developing cohesive visual identities for businesses of all sizes.'
                ],
                [
                    'title' => 'Marketing Materials Specialist',
                    'description' => 'Professional design of marketing and promotional materials. Including brochures, flyers, posters, and social media graphics. Ensuring consistent brand messaging across all materials.'
                ],
                [
                    'title' => 'Publication Layout Designer',
                    'description' => 'Expert in publication and editorial design. Services include magazine layouts, book design, annual reports, and newsletters. Creating engaging and readable layouts for print and digital media.'
                ],
                [
                    'title' => 'Package Design Professional',
                    'description' => 'Specialized in product packaging design and development. Including retail packaging, label design, and packaging graphics. Creating eye-catching designs that stand out on shelves.'
                ],
                [
                    'title' => 'Digital Graphics Expert',
                    'description' => 'Creation of custom digital graphics and illustrations. Specializing in web graphics, digital ads, infographics, and social media content. Optimized for various digital platforms and devices.'
                ]
            ],
            'Web Development' => [
                [
                    'title' => 'Full-Stack Web Developer',
                    'description' => 'Comprehensive web development services using modern technologies. Proficient in both frontend and backend development, creating responsive and scalable web applications. Expertise in React, Node.js, and related technologies.'
                ],
                [
                    'title' => 'E-commerce Website Specialist',
                    'description' => 'Expert in building custom e-commerce solutions. Including shopping cart implementation, payment gateway integration, and inventory management systems. Creating secure and user-friendly online stores.'
                ],
                [
                    'title' => 'WordPress Development Expert',
                    'description' => 'Specialized WordPress website development and customization. Services include custom theme development, plugin creation, and site optimization. Creating powerful and flexible WordPress solutions.'
                ],
                [
                    'title' => 'Frontend UI/UX Developer',
                    'description' => 'Creating engaging and responsive user interfaces. Focus on user experience, modern design principles, and interactive elements. Ensuring websites work seamlessly across all devices.'
                ],
                [
                    'title' => 'Web Application Security Specialist',
                    'description' => 'Expert in web application security and optimization. Including security audits, vulnerability testing, and implementation of security measures. Ensuring websites are secure and perform optimally.'
                ]
            ],
            'Tutoring Services' => [
                [
                    'title' => 'Mathematics Tutor (All Levels)',
                    'description' => 'Experienced mathematics tutor covering elementary to advanced levels. Including algebra, calculus, statistics, and test preparation. Personalized approach to help students understand and excel in mathematics.'
                ],
                [
                    'title' => 'Science Education Specialist',
                    'description' => 'Comprehensive science tutoring in physics, chemistry, and biology. Covering high school and undergraduate levels. Includes lab report assistance and exam preparation.'
                ],
                [
                    'title' => 'Language Arts Teacher',
                    'description' => 'Expert tutoring in English language arts and literature. Services include writing skills, reading comprehension, and literary analysis. Supporting students in developing strong communication skills.'
                ],
                [
                    'title' => 'Test Preparation Expert',
                    'description' => 'Specialized tutoring for standardized tests including SAT, ACT, and GRE. Covering test strategies, practice exercises, and subject matter review. Proven methods for score improvement.'
                ],
                [
                    'title' => 'ESL/IELTS Preparation Tutor',
                    'description' => 'Professional English as Second Language instruction. Including IELTS and TOEFL test preparation, conversation practice, and grammar instruction. Tailored lessons for all English proficiency levels.'
                ]
            ],
            'Pet Care' => [
                [
                    'title' => 'Professional Dog Walker',
                    'description' => 'Reliable dog walking service with flexible scheduling. Including individual and group walks, basic training reinforcement, and exercise activities. GPS tracking and detailed visit reports provided.'
                ],
                [
                    'title' => 'Pet Sitting Specialist',
                    'description' => 'Comprehensive pet sitting services in your home. Including feeding, medication administration, exercise, and companionship. Regular updates and photos provided during each visit.'
                ],
                [
                    'title' => 'Mobile Pet Grooming Expert',
                    'description' => 'Professional mobile pet grooming service at your location. Services include bathing, trimming, nail care, and specialty grooming. Using pet-friendly products and gentle handling techniques.'
                ],
                [
                    'title' => 'Pet Training Professional',
                    'description' => 'Experienced pet trainer offering behavior modification and training. Including basic obedience, house training, and addressing specific behavioral issues. Positive reinforcement methods used.'
                ],
                [
                    'title' => 'Pet Transportation Service',
                    'description' => 'Safe and reliable pet transportation service. Including vet visits, grooming appointments, and relocation assistance. Climate-controlled vehicle with proper safety equipment.'
                ]
            ],
            'Photography' => [
                [
                    'title' => 'Wedding Photography Specialist',
                    'description' => 'Professional wedding photography capturing your special day. Including engagement shoots, ceremony coverage, and reception photography. Artistic and photojournalistic styles available.'
                ],
                [
                    'title' => 'Portrait Photography Expert',
                    'description' => 'Creative portrait photography for individuals and families. Offering both studio and location shoots with professional lighting and posing guidance. Including retouching and digital delivery.'
                ],
                [
                    'title' => 'Commercial Product Photographer',
                    'description' => 'Specialized in product photography for e-commerce and advertising. Using professional lighting and techniques to showcase products at their best. Including photo editing and format optimization.'
                ],
                [
                    'title' => 'Event Photography Professional',
                    'description' => 'Comprehensive event photography coverage services. Including corporate events, parties, and special occasions. Quick turnaround time with professional editing included.'
                ],
                [
                    'title' => 'Real Estate Photographer',
                    'description' => 'Professional real estate photography services. Including interior and exterior shots, aerial drone photography, and virtual tours. Optimized images for real estate listings and marketing.'
                ]
            ]
        ];

        $category = BarterCategory::inRandomOrder()->first();
        $category_name = $category->name;

        $service = fake()->randomElement($category_details[$category_name] ?? []);
        $title = $service['title'] ?? 'Fallback Service';
        $description = $service['description'] ?? 'Fallback service description';

        return [
            'barter_provider_id' => fake()->numberBetween(1, 10),
            'barter_category_id' => $category->id,
            'title' => $title,
            'description' => $description,
            'min_price' => $min_price,
            'max_price' => $max_price,
            'price_unit' => 'session',
            'status' => fake()->randomElement($status),
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
