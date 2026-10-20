<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomepageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('homepages')->updateOrInsert(
            ['id' => 1],
            [
                'hero_section' => json_encode([
                    "title" => "Maximize Your Space Capacity & Warehouse Efficiency",
                    "subtitle" => "We provide integrated services for Premium Forklift Sales & Rental, Warehouse Racking Systems, and High-Quality Plastic Pallets. Engineered to minimize operational downtime and optimize your logistics flow.",
                    "cta_text" => "Request a Quote",
                    "hero_badge" => "Material Handling & Racking Specialist",
                    "hero_cta_link" => "https://wa.me/62xxxxxxxxxx",
                    "cta_text2" => "Sales Consultation (WA)",
                    "hero_cta_link2" => "https://wa.me/62xxxxxxxxxx",
                    "solutions" => [
                        "steps" => [
                            ["title" => "Forklift <b>Sales & Rental</b>", "description" => "Smooth handling without <b>downtime</b>"],
                            ["title" => "Warehouse <b>Racking</b>", "description" => "Optimize vertical space <b>capacity</b>"],
                            ["title" => "Plastic <b>Pallets</b>", "description" => "High durability & asset <b>protection</b>"],
                            ["title" => "Logistics <b>Solution</b>", "description" => "One-stop service from <b>experts</b>"]
                        ]
                    ]
                ]),
                'about_section' => json_encode([
                    "badge" => "TRUSTED LOGISTICS PARTNER",
                    "title" => "Warehouse Equipment Solutions to Minimize Workplace Risks & Downtime",
                    "description" => "Maintaining smooth material flow in a warehouse requires more than just functional equipment. We understand the strict occupational health and safety (OHS/K3) regulations in your industrial environment. Therefore, Keylock Indonesia ensures every forklift fleet is ready for use with valid and active SIA (Equipment License) and SIO (Operator License) documents. We partner with you to maintain the highest safety standards, minimize workplace incident risks, and ensure your operations always pass every safety audit.",
                    "bullet_points" => [
                        ["id" => 21, "text" => "Regulation-compliant units (Active SIA & SIO)"],
                        ["id" => 22, "text" => "Top-tier OHS/K3-certified fleet"],
                        ["id" => 23, "text" => "Local technicians standby across all areas"],
                        ["id" => 24, "text" => "Guaranteed back-up units to prevent downtime"],
                        ["id" => 25, "text" => "Flexible contracts & CAPEX savings"]
                    ],
                    "stats" => [
                        ["id" => 17, "number" => "1000+", "label" => "Completed Projects", "icon_name" => "briefcase"],
                        ["id" => 18, "number" => "500+", "label" => "Partner Clients", "icon_name" => "users"],
                        ["id" => 19, "number" => "24/7", "label" => "Technical Support", "icon_name" => "truck"],
                        ["id" => 20, "number" => "100%", "label" => "Extensive K3 Certification", "icon_name" => "shield-check"]
                    ]
                ]),
                'service_section' => json_encode([
                    "badge" => "OUR SERVICES",
                    "title" => "Integrated Material Handling Management Solutions",
                    "description" => "Everything you need to move, store, and manage your material flow efficiently and safely.",
                    "services" => [
                        ["icon_name" => "truck", "title" => "Forklift Procurement", "features" => ["Electric Forklift", "Diesel Forklift", "Reach Truck", "Hand Pallet"]],
                        ["icon_name" => "calendar-clock", "title" => "Flexible Forklift Rental", "features" => ["Daily & Weekly Rental", "Monthly Rental", "Long-Term Contract", "24/7 Technical Support"]],
                        ["icon_name" => "layers", "title" => "Warehouse Racking System", "features" => ["Selective Pallet Rack", "Drive-In Racking System", "Medium Duty Racking", "Heavy Duty Racking"]],
                        ["icon_name" => "box", "title" => "Industrial Plastic Pallets", "features" => ["Heavy Duty Series", "Export Grade Pallet", "Food Grade (Hygienic)"]]
                    ]
                ]),
                'advantages_section' => json_encode([
                    "badge" => "OUR ADVANTAGES",
                    "title" => "Full Commitment to Reliability & Operational Safety",
                    "advantages" => [
                        ["icon_name" => "dollar-sign", "title" => "Real Budget Efficiency"],
                        ["icon_name" => "user-check", "title" => "SIO-Certified Technicians"],
                        ["icon_name" => "globe", "title" => "Wide Network Mobilization"],
                        ["icon_name" => "shield-check", "title" => "K3 Audit-Compliant Units"],
                        ["icon_name" => "wrench", "title" => "Anti-Downtime Guarantee"],
                        ["icon_name" => "maximize", "title" => "Measured Space Optimization"]
                    ]
                ]),
                'products_section' => json_encode([
                    "badge" => "FEATURED PRODUCTS",
                    "title" => "Ready-to-Work Fleet & Equipment",
                    "categories" => [
                        ["name" => "Plastic Pallet", "slug" => "plastic-pallet"],
                        ["name" => "Storage Racks & Truck Scale", "slug" => "storage-racks-truck-scale"],
                        ["name" => "Forklift", "slug" => "forklift"],
                        ["name" => "Industrial Fan", "slug" => "industrial-fan"]
                    ]
                ]),
                'workflow_section' => json_encode([
                    "badge" => "OUR WORKFLOW",
                    "title" => "Structured Stages Toward Logistics Efficiency",
                    "steps" => [
                        ["title" => "Needs Consultation", "icon" => "message-square"],
                        ["title" => "Free Site Survey", "icon" => "map-pin"],
                        ["title" => "Solution Recommendation", "icon" => "git-pull-request"],
                        ["title" => "Installation & Commissioning", "icon" => "wrench"],
                        ["title" => "Maintenance Guarantee", "icon" => "shield"]
                    ]
                ]),
                'testimonials_section' => json_encode([
                    "badge" => "CLIENT TESTIMONIALS",
                    "testimonials" => [
                        ["name" => "Alfino Firmansyah", "quote" => "Their warehouse racking system successfully maximized our storage capacity..."],
                        ["name" => "Andi Pratama", "quote" => "Keylock's forklift fleet delivery was exceptionally punctual..."],
                        ["name" => "Budi Santoso", "quote" => "The flexible forklift rental scheme has significantly improved..."]
                    ]
                ]),
                'coverage_section' => json_encode([
                    "badge" => "COVERAGE AREA",
                    "title" => "Serving Industrial Needs Across Indonesia",
                    "cities" => ["Jakarta", "Bekasi", "Karawang", "Cikarang", "Subang", "Surabaya"]
                ]),
                'faq_section' => json_encode([
                    "badge" => "FAQ",
                    "title" => "Frequently Asked Questions",
                    "faqs" => [
                        ["question" => "How much is the forklift rental price?", "answer" => "Contact our team for the best quotation."],
                        ["question" => "Which service areas do you cover?", "answer" => "We serve major industrial areas across Indonesia."]
                    ]
                ]),
                'cta_section' => json_encode([
                    "title" => "Ready to Boost Your Warehouse Productivity?",
                    "btn_text1" => "Free Consultation",
                    "btn_link1" => "https://wa.me/62xxxxxxxxxx"
                ]),
                'footer_section' => json_encode([
                    "address" => "Jl. Arteri KIIC, Karawang",
                    "phone" => "+62 812 3456 7890",
                    "email" => "keylock@keylockindonesia.com"
                ]),
                'locale' => 'en',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}