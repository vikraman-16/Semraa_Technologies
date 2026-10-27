<?php
declare(strict_types=1);
// Field names must be unique within each service. Options are server-side allowlists.
return [
    'web-development' => [
        'name' => 'Web Development', 'description' => 'A faster, clearer home for your business online.', 'icon' => 'web',
        'extra_fields' => [
            ['type'=>'select','label'=>'Website type','name'=>'website_type','options'=>['Business website','E-commerce','Web application','Redesign'],'required'=>true],
            ['type'=>'number','label'=>'Estimated number of pages','name'=>'page_count','required'=>true,'min'=>1,'max'=>10000],
            ['type'=>'text','label'=>'Existing website URL','name'=>'existing_site','required'=>false],
            ['type'=>'select','label'=>'Budget range','name'=>'budget','options'=>['Under ₹50,000','₹50,000–₹1,50,000','₹1,50,000–₹5,00,000','Above ₹5,00,000','Help me estimate'],'required'=>true],
        ],
    ],
    'software-development' => [
        'name'=>'Software Development','description'=>'Purpose-built software for the way you work.','icon'=>'code',
        'extra_fields'=>[
            ['type'=>'select','label'=>'Application type','name'=>'application_type','options'=>['Business management','Customer portal','Mobile application','Integration','Other'],'required'=>true],
            ['type'=>'textarea','label'=>'Workflow you want to improve','name'=>'workflow','required'=>true],
            ['type'=>'number','label'=>'Expected users','name'=>'users','min'=>1,'max'=>1000000,'required'=>true],
        ],
    ],
    'cloud-devops'=>[
        'name'=>'Cloud & DevOps','description'=>'Infrastructure that is easier to release and operate.','icon'=>'cloud',
        'extra_fields'=>[
            ['type'=>'select','label'=>'Cloud provider','name'=>'cloud_provider','options'=>['AWS','Microsoft Azure','Google Cloud','On-premises','Not decided'],'required'=>true],
            ['type'=>'textarea','label'=>'Current infrastructure','name'=>'infrastructure','required'=>true],
            ['type'=>'number','label'=>'Engineering team size','name'=>'team_size','min'=>1,'max'=>100000,'required'=>true],
        ],
    ],
    'cybersecurity'=>[
        'name'=>'Cybersecurity','description'=>'Understand risk and prioritise practical improvements.','icon'=>'shield',
        'extra_fields'=>[
            ['type'=>'radio','label'=>'Assessment type','name'=>'assessment','options'=>['VAPT','SOC readiness','GRC review'],'required'=>true],
            ['type'=>'number','label'=>'Number of assets or IPs','name'=>'assets','min'=>1,'max'=>100000,'required'=>true],
            ['type'=>'checkbox','label'=>'Compliance needs','name'=>'compliance','options'=>['ISO 27001','SOC 2','PCI DSS','Internal policy','Not sure'],'required'=>false],
        ],
    ],
    'ui-ux'=>[
        'name'=>'UI/UX Design','description'=>'Make important tasks feel simple.','icon'=>'design',
        'extra_fields'=>[
            ['type'=>'select','label'=>'Design scope','name'=>'design_scope','options'=>['Website','Mobile app','Business software','Design system'],'required'=>true],
            ['type'=>'textarea','label'=>'Who will use the product?','name'=>'audience','required'=>true],
        ],
    ],
    'it-consulting'=>[
        'name'=>'IT Consulting','description'=>'A practical next step for your technology decisions.','icon'=>'compass',
        'extra_fields'=>[
            ['type'=>'select','label'=>'Your priority','name'=>'priority','options'=>['Technology roadmap','System review','Vendor selection','Digital transformation'],'required'=>true],
            ['type'=>'textarea','label'=>'Decision you need help with','name'=>'decision','required'=>true],
        ],
    ],
    'semraa-nexus-db'=>[
        'name'=>'Semraa Nexus DB','description'=>'Discuss your business data and reporting requirements.','icon'=>'database',
        'extra_fields'=>[
            ['type'=>'number','label'=>'Team size','name'=>'team_size','min'=>1,'max'=>100000,'required'=>true],
            ['type'=>'text','label'=>'Current tools','name'=>'current_tools','required'=>true],
            ['type'=>'textarea','label'=>'Your use case','name'=>'use_case','required'=>true],
        ],
    ],
    'work-pulse'=>[
        'name'=>'Work Pulse','description'=>'Explore a clearer view of your team’s work.','icon'=>'pulse',
        'extra_fields'=>[
            ['type'=>'number','label'=>'Team size','name'=>'team_size','min'=>1,'max'=>100000,'required'=>true],
            ['type'=>'text','label'=>'Current tools','name'=>'current_tools','required'=>true],
            ['type'=>'textarea','label'=>'Your use case','name'=>'use_case','required'=>true],
        ],
    ],
];
