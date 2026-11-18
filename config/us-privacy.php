<?php

use App\Models\User;

/*
 * The rules of each state law, as compiled in November 2026 from the statutes and the
 * attorney general guidance. Laws change: verify with counsel and override what you need
 * (publish this file). California has its own suite: jeffersongoncalves/laravel-ccpa.
 *
 * thresholds (a controller is covered when any line matches; revenue, when set, must also be met):
 *   consumers              personal data of at least this many consumers a year (0 = no count: Texas, Nebraska)
 *   consumers_with_sale    ...or of this many consumers while deriving
 *   sale_revenue_share     ...at least this % of gross revenue from selling personal data (0 = any revenue or discount)
 *   revenue                annual gross revenue in USD that must also be exceeded (Utah, Tennessee)
 *   any_sensitive / any_sale  covered whenever it processes sensitive data / sells personal data (Connecticut)
 * response_days + extension_days   deadline to answer a request, extendable once
 * appeal_days + appeal_extension_days   deadline to decide an appeal of a refusal (null = no appeal)
 * sensitive        opt_in (consent), opt_out (notice and a right to opt out) or strictly_necessary (Maryland)
 * universal_opt_out  date from which an opt-out preference signal (Global Privacy Control) must be honored
 * assessments      data protection assessments required for targeted advertising, sale, profiling, sensitive data
 * minor_age / minor_rule  known consumers under this age: consent (or a ban) for targeted advertising and sale
 */
$rights = ['access', 'correct', 'delete', 'portability', 'opt_out_sale', 'opt_out_targeted_advertising', 'opt_out_profiling'];
$virginiaModel = [
    'response_days' => 45,
    'extension_days' => 45,
    'appeal_days' => 60,
    'appeal_extension_days' => 0,
    'sensitive' => 'opt_in',
    'universal_opt_out' => null,
    'assessments' => true,
    'minor_age' => 13,
    'minor_rule' => 'consent',
    'rights' => $rights,
];

return [

    /*
     * Eloquent models holding personal data. Each must implement
     * JeffersonGoncalves\UsPrivacy\Contracts\HasPersonalData (use the
     * InteractsWithPersonalData trait) and declare #[PersonalData] attributes.
     */
    'models' => [
        User::class,
    ],

    /*
     * Key for the "hash" anonymize strategy. Falls back to app.key.
     * Rotating it changes every future pseudonym.
     */
    'hash_key' => env('US_PRIVACY_HASH_KEY'),

    /*
     * Where the visitor's state comes from: a request header holding the two-letter code
     * (Cloudflare's "Add visitor location headers" managed transform sends cf-region-code),
     * read only when the country header says US. Or call UsPrivacy::resolveStateUsing().
     */
    'state_header' => 'cf-region-code',
    'country_header' => 'cf-ipcountry',

    /*
     * Rules for consumers whose state is unknown: "strictest" combines every state in force
     * (shortest deadlines, opt-in, universal opt-out honored), or a state code.
     */
    'unknown_state' => 'strictest',

    'states' => [
        'VA' => ['name' => 'Virginia', 'law' => 'Virginia Consumer Data Protection Act', 'citation' => 'Va. Code §59.1-575 et seq.', 'effective' => '2023-01-01',
            'thresholds' => ['consumers' => 100000, 'consumers_with_sale' => 25000, 'sale_revenue_share' => 50]] + $virginiaModel,
        'CO' => ['name' => 'Colorado', 'law' => 'Colorado Privacy Act', 'citation' => 'C.R.S. §6-1-1301 et seq.', 'effective' => '2023-07-01',
            'thresholds' => ['consumers' => 100000, 'consumers_with_sale' => 25000, 'sale_revenue_share' => 0],
            'appeal_days' => 45, 'appeal_extension_days' => 60, 'universal_opt_out' => '2024-07-01', 'minor_age' => 18] + $virginiaModel,
        'CT' => ['name' => 'Connecticut', 'law' => 'Connecticut Data Privacy Act', 'citation' => 'Conn. Gen. Stat. §42-515 et seq.', 'effective' => '2023-07-01',
            'thresholds' => ['consumers' => 35000, 'any_sensitive' => true, 'any_sale' => true],
            'universal_opt_out' => '2025-01-01', 'minor_age' => 18] + $virginiaModel,
        'UT' => ['name' => 'Utah', 'law' => 'Utah Consumer Privacy Act', 'citation' => 'Utah Code §13-61-101 et seq.', 'effective' => '2023-12-31',
            'thresholds' => ['consumers' => 100000, 'consumers_with_sale' => 25000, 'sale_revenue_share' => 50, 'revenue' => 25000000],
            'appeal_days' => null, 'sensitive' => 'opt_out', 'assessments' => false,
            'rights' => ['access', 'delete', 'portability', 'opt_out_sale', 'opt_out_targeted_advertising']] + $virginiaModel,
        'TX' => ['name' => 'Texas', 'law' => 'Texas Data Privacy and Security Act', 'citation' => 'Tex. Bus. & Com. Code §541.001 et seq.', 'effective' => '2024-07-01',
            'thresholds' => ['consumers' => 0], 'universal_opt_out' => '2025-01-01'] + $virginiaModel,
        'OR' => ['name' => 'Oregon', 'law' => 'Oregon Consumer Privacy Act', 'citation' => 'ORS 646A.570 et seq.', 'effective' => '2024-07-01',
            'thresholds' => ['consumers' => 100000, 'consumers_with_sale' => 25000, 'sale_revenue_share' => 25],
            'appeal_days' => 45, 'appeal_extension_days' => 45, 'universal_opt_out' => '2026-01-01', 'minor_age' => 16,
            'rights' => [...$rights, 'list_third_parties']] + $virginiaModel,
        'MT' => ['name' => 'Montana', 'law' => 'Montana Consumer Data Privacy Act', 'citation' => 'Mont. Code §30-14-2801 et seq.', 'effective' => '2024-10-01',
            'thresholds' => ['consumers' => 25000, 'consumers_with_sale' => 15000, 'sale_revenue_share' => 25],
            'universal_opt_out' => '2025-01-01', 'minor_age' => 18] + $virginiaModel,
        'IA' => ['name' => 'Iowa', 'law' => 'Iowa Consumer Data Protection Act', 'citation' => 'Iowa Code ch. 715D', 'effective' => '2025-01-01',
            'thresholds' => ['consumers' => 100000, 'consumers_with_sale' => 25000, 'sale_revenue_share' => 50],
            'response_days' => 90, 'sensitive' => 'opt_out', 'assessments' => false,
            'rights' => ['access', 'delete', 'portability', 'opt_out_sale', 'opt_out_targeted_advertising']] + $virginiaModel,
        'DE' => ['name' => 'Delaware', 'law' => 'Delaware Personal Data Privacy Act', 'citation' => '6 Del. C. §12D-101 et seq.', 'effective' => '2025-01-01',
            'thresholds' => ['consumers' => 35000, 'consumers_with_sale' => 10000, 'sale_revenue_share' => 20],
            'universal_opt_out' => '2026-01-01', 'minor_age' => 18, 'rights' => [...$rights, 'list_third_parties']] + $virginiaModel,
        'NE' => ['name' => 'Nebraska', 'law' => 'Nebraska Data Privacy Act', 'citation' => 'Neb. Rev. Stat. §87-1101 et seq.', 'effective' => '2025-01-01',
            'thresholds' => ['consumers' => 0], 'universal_opt_out' => '2025-01-01'] + $virginiaModel,
        'NH' => ['name' => 'New Hampshire', 'law' => 'New Hampshire Privacy Act', 'citation' => 'RSA 507-H', 'effective' => '2025-01-01',
            'thresholds' => ['consumers' => 35000, 'consumers_with_sale' => 10000, 'sale_revenue_share' => 25],
            'universal_opt_out' => '2025-01-01', 'minor_age' => 16] + $virginiaModel,
        'NJ' => ['name' => 'New Jersey', 'law' => 'New Jersey Data Privacy Act', 'citation' => 'N.J.S.A. 56:8-166.4 et seq.', 'effective' => '2025-01-15',
            'thresholds' => ['consumers' => 100000, 'consumers_with_sale' => 25000, 'sale_revenue_share' => 0],
            'universal_opt_out' => '2025-07-15', 'minor_age' => 17] + $virginiaModel,
        'TN' => ['name' => 'Tennessee', 'law' => 'Tennessee Information Protection Act', 'citation' => 'Tenn. Code §47-18-3201 et seq.', 'effective' => '2025-07-01',
            'thresholds' => ['consumers' => 175000, 'consumers_with_sale' => 25000, 'sale_revenue_share' => 50, 'revenue' => 25000000]] + $virginiaModel,
        'MN' => ['name' => 'Minnesota', 'law' => 'Minnesota Consumer Data Privacy Act', 'citation' => 'Minn. Stat. §325M.10 et seq.', 'effective' => '2025-07-31',
            'thresholds' => ['consumers' => 100000, 'consumers_with_sale' => 25000, 'sale_revenue_share' => 25],
            'appeal_days' => 45, 'appeal_extension_days' => 60, 'universal_opt_out' => '2025-07-31', 'minor_age' => 16,
            'rights' => [...$rights, 'list_third_parties', 'question_profiling']] + $virginiaModel,
        'MD' => ['name' => 'Maryland', 'law' => 'Maryland Online Data Privacy Act', 'citation' => 'Md. Code, Com. Law §14-4701 et seq.', 'effective' => '2025-10-01',
            'thresholds' => ['consumers' => 35000, 'consumers_with_sale' => 10000, 'sale_revenue_share' => 20],
            'sensitive' => 'strictly_necessary', 'universal_opt_out' => '2025-10-01', 'minor_age' => 18, 'minor_rule' => 'prohibited',
            'rights' => [...$rights, 'list_third_parties']] + $virginiaModel,
        'IN' => ['name' => 'Indiana', 'law' => 'Indiana Consumer Data Protection Act', 'citation' => 'Ind. Code §24-15', 'effective' => '2026-01-01',
            'thresholds' => ['consumers' => 100000, 'consumers_with_sale' => 25000, 'sale_revenue_share' => 50]] + $virginiaModel,
        'KY' => ['name' => 'Kentucky', 'law' => 'Kentucky Consumer Data Protection Act', 'citation' => 'KRS 367.3611 et seq.', 'effective' => '2026-01-01',
            'thresholds' => ['consumers' => 100000, 'consumers_with_sale' => 25000, 'sale_revenue_share' => 50]] + $virginiaModel,
        'RI' => ['name' => 'Rhode Island', 'law' => 'Rhode Island Data Transparency and Privacy Protection Act', 'citation' => 'R.I. Gen. Laws §6-48.1-1 et seq.', 'effective' => '2026-01-01',
            'thresholds' => ['consumers' => 35000, 'consumers_with_sale' => 10000, 'sale_revenue_share' => 20]] + $virginiaModel,
    ],

];
