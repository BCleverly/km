<?php

namespace Database\Seeders;

use App\Models\Fantasy;
use App\Models\User;
use App\ContentStatus;
use Illuminate\Database\Seeder;

class FantasySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run the DatabaseSeeder first.');
            return;
        }

        $fantasies = [
            'I fantasize about being completely at someone\'s mercy, where every decision is made for me and I have no choice but to obey. The power exchange is intoxicating - knowing that my pleasure and discomfort are entirely in their hands.',
            'There\'s something incredibly arousing about being watched while I perform intimate tasks. The idea of being observed, judged, and potentially rewarded or punished based on my performance sends shivers down my spine.',
            'I dream of a scenario where I\'m given a series of increasingly challenging tasks, each one pushing my boundaries further. The anticipation of not knowing what comes next, combined with the knowledge that failure has consequences, is thrilling.',
            'The thought of being bound and teased for hours, with my partner controlling every aspect of my pleasure, drives me wild. The denial, the anticipation, the eventual release - it\'s all part of the perfect fantasy.',
            'I fantasize about being in a public setting where I have to maintain composure while being secretly controlled. The risk of discovery, the need to stay focused, and the secret knowledge that I\'m being directed from afar is incredibly exciting.',
            'There\'s something deeply satisfying about being given specific instructions and having to follow them precisely. The structure, the rules, the clear expectations - it creates a sense of security and arousal that\'s hard to describe.',
            'I dream of a scenario where my partner reads my body language perfectly, knowing exactly when to push me further and when to pull back. The intuitive understanding and the way they can read my desires without words is incredibly intimate.',
            'The idea of being trained, molded, and shaped into exactly what my partner desires is both humbling and incredibly arousing. The process of transformation, of becoming their perfect creation, is a fantasy that never gets old.',
            'I fantasize about being in a situation where I have to earn every touch, every kiss, every moment of pleasure. The work, the effort, the dedication required to receive affection makes every reward feel incredibly meaningful.',
            'There\'s something incredibly sexy about being given a task that I know I\'ll struggle with, but being supported and encouraged throughout the process. The challenge combined with the support creates a perfect balance of arousal and comfort.',
            'I dream of a scenario where my partner uses technology to control me remotely, sending me tasks and monitoring my progress. The modern twist on traditional power exchange, combined with the constant connection, is incredibly exciting.',
            'The thought of being blindfolded and having to rely entirely on my other senses while being guided through various experiences is both terrifying and incredibly arousing. The vulnerability and trust required is intoxicating.',
            'I fantasize about being in a situation where I have to choose between my own comfort and my partner\'s pleasure, and always choosing their pleasure. The selflessness, the sacrifice, the way it makes me feel to prioritize them is deeply satisfying.',
            'There\'s something incredibly arousing about being given a time limit for a task and having to work against the clock. The pressure, the urgency, the way my body responds to the stress - it\'s a unique kind of excitement.',
            'I dream of a scenario where my partner creates a detailed plan for our time together, with every moment mapped out and every possibility considered. The thoroughness, the attention to detail, the way they\'ve thought of everything is incredibly attractive.',
            'The idea of being given a series of small, seemingly insignificant tasks that build up to something much larger is fascinating. The way each small step contributes to a bigger picture, the patience required, the eventual payoff - it\'s all part of the appeal.',
            'I fantasize about being in a situation where I have to communicate my needs and desires without using words, relying entirely on body language and subtle cues. The challenge of expression, the intimacy of understanding, the way it deepens our connection is incredible.',
            'There\'s something deeply satisfying about being given a task that requires me to be creative and think outside the box. The mental stimulation, the problem-solving, the way it engages both my mind and body is incredibly arousing.',
            'I dream of a scenario where my partner sets up a series of challenges that test different aspects of my abilities - physical, mental, emotional. The variety, the way each challenge reveals something new about myself, the growth that comes from pushing my limits is thrilling.',
            'The thought of being given a task that I know will be difficult, but being promised a reward that makes all the effort worthwhile, is incredibly motivating. The clear cause and effect, the tangible goal, the way it focuses my energy is deeply satisfying.',
            'I fantasize about being in a situation where I have to trust my partner completely, knowing that they have my best interests at heart even when they\'re pushing me beyond my comfort zone. The trust, the safety, the way it allows me to let go completely is intoxicating.',
            'There\'s something incredibly sexy about being given a task that requires me to be vulnerable and open, knowing that my partner will handle that vulnerability with care and respect. The courage required, the way it deepens our connection, the intimacy that comes from sharing my true self is beautiful.',
            'I dream of a scenario where my partner creates a fantasy world for us to explore together, with its own rules, its own logic, its own possibilities. The escapism, the creativity, the way it allows us to be different versions of ourselves is incredibly appealing.',
            'The idea of being given a task that I know I\'ll enjoy, but having to wait and work for it, makes the eventual experience so much more intense. The anticipation, the build-up, the way it heightens every sensation is incredible.',
            'I fantasize about being in a situation where my partner knows exactly how to push my buttons, both literally and figuratively, and uses that knowledge to create the perfect experience for both of us. The understanding, the skill, the way they can read and respond to my needs is incredibly attractive.',
        ];

        foreach ($fantasies as $content) {
            Fantasy::create([
                'content' => $content,
                'user_id' => $users->random()->id,
                'status' => ContentStatus::Approved->value,
                'is_premium' => false,
                'view_count' => rand(0, 1000),
                'report_count' => 0,
            ]);
        }

        $this->command->info('Created 25 fantasies successfully.');
    }
}