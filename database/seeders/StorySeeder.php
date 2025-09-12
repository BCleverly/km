<?php

namespace Database\Seeders;

use App\Models\Story;
use App\Models\User;
use App\Models\Models\Tag;
use App\ContentStatus;
use Illuminate\Database\Seeder;

class StorySeeder extends Seeder
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

        // Get all available tags from the system
        $availableTags = Tag::all();
        
        if ($availableTags->isEmpty()) {
            $this->command->warn('No tags found. Please ensure TaskDataSeeder runs before StorySeeder.');
        }

        $stories = [
            [
                'title' => 'The First Task',
                'summary' => 'A newcomer\'s journey into the world of assigned tasks and the surprising rewards that await.',
                'content' => 'I never thought I\'d find myself here, staring at my first assigned task on the screen. The instructions were clear, but my heart was racing. "Complete this task within 24 hours," it read. The anticipation was almost unbearable.

As I began, I realized this wasn\'t just about following instructions - it was about trust, about letting go of control, about embracing the unknown. Each step brought new sensations, new discoveries about myself that I never knew existed.

When I finally completed the task and received my first reward, the feeling was indescribable. It wasn\'t just the physical pleasure, though that was incredible. It was the sense of accomplishment, of having pushed myself beyond my comfort zone, of having trusted the process completely.

That first task changed everything. It opened doors I didn\'t know existed and showed me a side of myself I never knew was there. Now, I can\'t imagine my life without these challenges, without the structure they provide, without the growth they inspire.

The journey had only just begun, but I already knew I was exactly where I was meant to be.',
            ],
            [
                'title' => 'The Midnight Challenge',
                'summary' => 'A late-night task that pushes boundaries and reveals hidden desires.',
                'content' => 'The notification came at 11:47 PM. I was already in bed, ready to sleep, when my phone buzzed with a new task. "Complete before midnight," it instructed. My heart skipped a beat.

The task was unlike anything I\'d done before. It required complete silence, perfect timing, and absolute focus. As I began, I could feel the tension building with each passing minute. The clock was ticking, and I was racing against time.

Every sound seemed amplified in the quiet of the night. Every movement felt more deliberate, more meaningful. The darkness added an extra layer of intensity to the experience, making every sensation more acute, every moment more precious.

When I finally completed the task with just minutes to spare, the relief was overwhelming. But more than that, I felt a sense of accomplishment that I\'d never experienced before. I had pushed myself beyond what I thought was possible, and the reward was worth every moment of effort.

That midnight challenge taught me that sometimes the best experiences come when we least expect them, and that pushing our boundaries can lead to the most incredible discoveries.',
            ],
            [
                'title' => 'The Weekend Retreat',
                'summary' => 'A couple\'s journey through a series of connected tasks during a romantic getaway.',
                'content' => 'We had planned this weekend for months. A quiet cabin in the woods, just the two of us, away from the distractions of daily life. What we didn\'t expect was the series of tasks that would transform our simple retreat into something extraordinary.

The first task arrived as we were unpacking. "Explore your surroundings together," it read. Simple enough, we thought. But as we walked through the forest, hand in hand, we began to see our environment in a completely new way. Every tree, every stream, every clearing became part of our shared experience.

The tasks continued throughout the weekend, each one building on the last, each one deepening our connection in ways we never imagined. We found ourselves communicating without words, understanding each other\'s needs and desires in ways that surprised us both.

By the end of the weekend, we weren\'t just a couple who had completed some tasks - we were partners who had discovered new depths of intimacy and trust. The experience had brought us closer together in ways that words alone could never achieve.

We returned home changed, carrying with us the lessons we\'d learned and the connection we\'d deepened. The weekend retreat had become so much more than we\'d planned, and we were grateful for every moment of it.',
            ],
            [
                'title' => 'The Public Test',
                'summary' => 'A challenging task that must be completed in a public setting while maintaining composure.',
                'content' => 'The task was clear: "Complete this in a public place, maintaining perfect composure throughout." My heart raced as I read the instructions. This was unlike anything I\'d attempted before.

I chose a busy coffee shop, thinking the ambient noise and activity would provide some cover. As I began the task, I realized how difficult it was to maintain focus while surrounded by people going about their daily lives. Every sound, every movement, every conversation threatened to break my concentration.

The challenge wasn\'t just physical - it was mental. I had to stay completely present, completely focused, while appearing completely normal to everyone around me. It was a test of willpower, of self-control, of the ability to compartmentalize my experience.

As the task progressed, I found myself entering a state of hyper-awareness. I could hear every conversation, see every detail of my surroundings, feel every sensation with incredible clarity. The public setting had somehow intensified the entire experience.

When I finally completed the task, the relief was overwhelming. But more than that, I felt a sense of accomplishment that came from having successfully navigated such a complex challenge. I had proven to myself that I could maintain control in any situation, and that knowledge was incredibly empowering.

The public test had been exactly that - a test of my abilities, my focus, and my determination. And I had passed with flying colors.',
            ],
            [
                'title' => 'The Learning Curve',
                'summary' => 'A beginner\'s experience with their first series of connected tasks.',
                'content' => 'I was nervous about my first series of connected tasks. The idea of multiple tasks building on each other seemed overwhelming, but I was determined to see it through. Little did I know how much I would learn about myself in the process.

The first task was simple enough - a basic introduction to the process. But as I completed it, I realized there was so much more to learn. Each task taught me something new about my own capabilities, my own desires, my own limits.

The second task built on the first, requiring me to apply what I\'d learned while pushing me slightly beyond my comfort zone. I was surprised by how naturally the progression felt, how each step prepared me for the next.

By the third task, I was beginning to understand the rhythm, the flow, the way each experience connected to the others. I was no longer just completing tasks - I was participating in a journey of self-discovery.

The final task in the series was the most challenging yet, but by then I had the confidence and the skills to approach it with enthusiasm rather than fear. I had learned to trust the process, to embrace the unknown, to find pleasure in the challenge itself.

When the series was complete, I felt like a different person. I had grown, I had learned, I had discovered aspects of myself that I never knew existed. The learning curve had been steep, but the rewards had been immeasurable.',
            ],
            [
                'title' => 'The Unexpected Reward',
                'summary' => 'A story about receiving a reward that was completely different from what was expected.',
                'content' => 'I had been working on a particularly challenging task for days. The instructions were complex, the requirements were demanding, and I wasn\'t sure I would be able to complete it successfully. But I persevered, determined to see it through to the end.

When I finally completed the task, I was exhausted but satisfied. I had done my best, and I was ready to accept whatever reward came my way. But what I received was completely unexpected.

Instead of the reward I had anticipated, I was given something entirely different - something that turned out to be exactly what I needed, even though I didn\'t know it at the time. The surprise was delightful, the experience was transformative, and the outcome was better than anything I could have imagined.

The unexpected reward taught me an important lesson about keeping an open mind and being receptive to new experiences. Sometimes the best things come in packages we don\'t expect, and sometimes the most meaningful rewards are the ones we never saw coming.

That experience changed my perspective on the entire process. I learned to approach each task with curiosity rather than expectation, to embrace the unknown rather than fear it, and to find joy in the surprises that life has to offer.

The unexpected reward had been exactly that - unexpected, but also exactly what I needed to grow and evolve in ways I never imagined possible.',
            ],
            [
                'title' => 'The Long Distance Connection',
                'summary' => 'A couple separated by distance finds new ways to connect through shared tasks.',
                'content' => 'Being in a long-distance relationship was challenging enough, but when we discovered the world of shared tasks, everything changed. Suddenly, we had a way to connect that transcended the physical distance between us.

The first task we completed together was simple - a basic exercise that we could both do simultaneously, even though we were thousands of miles apart. But the connection we felt was immediate and profound. We were sharing an experience, even though we were in different places.

As we continued with more complex tasks, we found ourselves developing new ways of communicating, new methods of sharing our experiences, new techniques for maintaining intimacy across the distance. The tasks became our bridge, our way of staying connected in meaningful ways.

The technology that enabled our long-distance relationship also enabled our shared experiences. We could see each other, hear each other, share in each other\'s reactions and responses. The distance became irrelevant as we focused on the connection we were building.

By the time we were finally reunited, we had developed a level of intimacy and understanding that we never would have achieved without those shared experiences. The long-distance connection had become our strength, our foundation, our way of building something beautiful despite the challenges we faced.

The distance had taught us that connection isn\'t about physical proximity - it\'s about shared experiences, mutual understanding, and the willingness to find new ways to be together, even when apart.',
            ],
            [
                'title' => 'The Morning Routine',
                'summary' => 'How a simple morning task transformed an entire day and changed a perspective on routine.',
                'content' => 'I\'ve never been a morning person. Waking up early has always been a struggle, and my morning routine was something I endured rather than enjoyed. But when I received my first morning task, everything changed.

The task was simple: "Start your day with intention and purpose." But the implications were profound. Instead of dragging myself out of bed and going through the motions, I was being asked to approach my morning with mindfulness and awareness.

As I began to incorporate the task into my daily routine, I noticed changes immediately. My mornings became more peaceful, more focused, more meaningful. I was no longer just getting through the day - I was starting it with purpose and intention.

The task required me to be present, to pay attention to the details of my morning routine, to find joy in the simple pleasures of starting a new day. It was a small change, but it had a big impact on my entire outlook.

By the end of the week, I was looking forward to my mornings in a way I never had before. The routine had become a ritual, a sacred time for setting intentions and preparing for the day ahead. I had transformed something I dreaded into something I cherished.

The morning routine had taught me that sometimes the smallest changes can have the biggest impact, and that approaching familiar activities with new awareness can transform them completely.',
            ],
            [
                'title' => 'The Group Challenge',
                'summary' => 'A community of users working together to complete a complex, multi-part task.',
                'content' => 'When the group challenge was announced, I was intrigued but hesitant. Working with others on such intimate tasks seemed daunting, but the opportunity to be part of something larger than myself was too compelling to resist.

The challenge was designed to be completed by a group of users working together, each person contributing their unique skills and perspectives to achieve a common goal. It was unlike anything I\'d experienced before.

As we began to work together, I was amazed by the level of trust and cooperation that developed among us. We were strangers, brought together by a shared interest, but we quickly became a team united by a common purpose.

The challenge required us to communicate openly, to share our experiences, to support each other through difficult moments. It was a test of our ability to work together, to trust each other, to find strength in our collective efforts.

By the time we completed the challenge, we had formed bonds that went beyond the task itself. We had become a community, a group of people who had shared something meaningful and transformative together.

The group challenge had shown me that sometimes the most rewarding experiences come from working with others, and that the connections we form through shared challenges can be some of the most meaningful relationships we ever develop.',
            ],
            [
                'title' => 'The Reflection Task',
                'summary' => 'A contemplative task that leads to deep self-discovery and personal growth.',
                'content' => 'The reflection task was different from anything I\'d done before. Instead of requiring physical action, it asked me to look inward, to examine my thoughts, feelings, and experiences with honesty and openness.

The task began with a simple instruction: "Spend time reflecting on your journey so far." But as I began to think about my experiences, I realized how much I had grown and changed since I first started this journey.

I thought about the challenges I\'d faced, the fears I\'d overcome, the discoveries I\'d made about myself. I reflected on the moments of doubt and the moments of triumph, the times when I wanted to give up and the times when I felt unstoppable.

The reflection process was emotional and intense, but it was also incredibly healing. I began to see patterns in my behavior, connections between my experiences, and insights into my own nature that I had never recognized before.

By the end of the task, I felt like I had a deeper understanding of myself than I ever had before. The reflection had been a gift, an opportunity to pause and appreciate how far I\'d come and how much I\'d learned.

The reflection task had taught me that sometimes the most important work we can do is the work of understanding ourselves, and that taking time to reflect on our experiences can lead to the most profound insights and growth.',
            ],
            [
                'title' => 'The Surprise Element',
                'summary' => 'A task that included an unexpected twist that changed everything.',
                'content' => 'I thought I knew what to expect from this task. The instructions seemed straightforward, the requirements were clear, and I felt confident in my ability to complete it successfully. But then came the surprise element.

Halfway through the task, everything changed. The rules shifted, the requirements evolved, and I found myself facing a completely different challenge than the one I had prepared for. The surprise was both exciting and terrifying.

At first, I felt frustrated and confused. I had planned everything so carefully, and now my plans were useless. But as I began to adapt to the new circumstances, I realized that the surprise element was actually a gift.

The unexpected twist forced me to think creatively, to adapt quickly, to find new solutions to new problems. It pushed me beyond my comfort zone in ways I never would have chosen for myself, but the results were incredible.

By the time I completed the task, I felt more confident and capable than I ever had before. The surprise element had taught me that I was more adaptable and resourceful than I knew, and that sometimes the best experiences come from the unexpected.

The surprise element had been exactly that - a surprise, but also an opportunity to discover strengths I never knew I had and to experience growth I never would have achieved without it.',
            ],
            [
                'title' => 'The Graduation',
                'summary' => 'Completing a series of advanced tasks and the sense of accomplishment that follows.',
                'content' => 'After months of working through increasingly complex tasks, I had finally reached the end of the advanced series. The graduation task was the culmination of everything I had learned, everything I had experienced, everything I had become.

The task itself was challenging, requiring me to draw on all the skills and knowledge I had developed over the course of my journey. But more than that, it was a celebration of how far I had come and how much I had grown.

As I worked through the final task, I found myself reflecting on my entire journey. I remembered the nervous beginner I had been, the fears I had faced, the challenges I had overcome. I thought about the person I had become and the confidence I had developed.

The graduation was more than just completing a series of tasks - it was a recognition of my growth, my dedication, my transformation. It was a moment to celebrate not just what I had accomplished, but who I had become in the process.

When I finally completed the graduation task, the sense of accomplishment was overwhelming. I had not only finished what I had started, but I had exceeded my own expectations and discovered capabilities I never knew I possessed.

The graduation had been a journey of self-discovery, personal growth, and transformation. It had taught me that with dedication, courage, and an open mind, we can achieve things we never thought possible and become people we never knew we could be.',
            ],
            [
                'title' => 'The Mentor\'s Guidance',
                'summary' => 'Learning from an experienced user who becomes a mentor and guide.',
                'content' => 'I was struggling with a particularly difficult task when I received a message from an experienced user offering to help. At first, I was hesitant to accept the offer - I wanted to figure things out on my own. But as the challenges continued to mount, I realized I needed guidance.

The mentor was patient and understanding, offering advice without judgment and support without pressure. They shared their own experiences, their own struggles, and their own triumphs, helping me to see that my challenges were not unique and that my goals were achievable.

Through their guidance, I began to understand the deeper aspects of the experience. It wasn\'t just about completing tasks - it was about personal growth, self-discovery, and the development of skills that would serve me in all areas of life.

The mentor taught me to approach challenges with curiosity rather than fear, to see setbacks as opportunities for learning, and to find joy in the process rather than just the outcome. Their wisdom and experience became a beacon of light in my journey.

By the time I had completed the difficult task, I felt like I had not only achieved my goal but had also gained a deeper understanding of myself and the process. The mentor\'s guidance had been invaluable, and I was grateful for their patience and wisdom.

The mentor had shown me that sometimes the greatest gift we can give or receive is the gift of guidance, and that sharing our experiences and knowledge can help others on their own journeys of growth and discovery.',
            ],
            [
                'title' => 'The Creative Challenge',
                'summary' => 'A task that required artistic expression and creative thinking.',
                'content' => 'The creative challenge was unlike anything I\'d encountered before. Instead of following specific instructions, I was asked to express myself artistically, to create something that reflected my experiences and emotions. The freedom was both exciting and intimidating.

I had never considered myself particularly creative, but the task required me to explore that side of myself. I began to experiment with different forms of expression, trying to find the right way to communicate what I was feeling and experiencing.

The creative process was challenging and rewarding in equal measure. I found myself discovering new aspects of my personality, new ways of thinking, new methods of expression. The task was pushing me to explore parts of myself I had never accessed before.

As I worked on my creative project, I began to see connections between my artistic expression and my personal growth. The creative challenge was not just about producing something beautiful - it was about understanding myself better and expressing that understanding in a meaningful way.

When I finally completed my creative project, I was amazed by what I had produced. It was more than just a response to a task - it was a reflection of my journey, my growth, and my understanding of myself. The creative challenge had given me a new way to see and express my experiences.

The creative challenge had taught me that creativity is not just about artistic ability - it\'s about finding new ways to understand and express our experiences, and that sometimes the most meaningful creations come from the deepest parts of ourselves.',
            ],
            [
                'title' => 'The Patience Test',
                'summary' => 'A task that required waiting and the lessons learned from delayed gratification.',
                'content' => 'The patience test was one of the most challenging tasks I\'d ever encountered, not because it was physically demanding, but because it required me to wait. The instructions were clear: "Complete this task, then wait 48 hours before receiving your reward."

At first, the waiting seemed unbearable. I had completed the task successfully, and I wanted immediate gratification. The delay felt like a punishment rather than a reward, and I found myself questioning the entire process.

But as the hours passed, I began to understand the purpose of the waiting period. It was teaching me patience, yes, but it was also teaching me to appreciate the anticipation, to find joy in the waiting, to understand that sometimes the best experiences come to those who can wait.

The waiting period became a time of reflection and anticipation. I thought about the task I had completed, the effort I had put into it, and the reward that was coming. The anticipation itself became a form of pleasure, a way of extending and intensifying the experience.

When the 48 hours finally passed and I received my reward, the satisfaction was incredible. The waiting had made the reward more meaningful, more appreciated, more valued. The delayed gratification had been worth every moment of anticipation.

The patience test had taught me that sometimes the best things in life are worth waiting for, and that the ability to delay gratification can lead to more meaningful and satisfying experiences. The waiting had been a gift, not a burden.',
            ],
            [
                'title' => 'The Trust Exercise',
                'summary' => 'A task that required complete trust and the vulnerability that comes with it.',
                'content' => 'The trust exercise was the most vulnerable I had ever felt. The task required me to place complete trust in the process, to let go of control, to surrender to the experience without knowing what would happen next. It was terrifying and exhilarating in equal measure.

I had always been someone who needed to know what was coming, who liked to plan and prepare and understand every aspect of an experience. But this task demanded that I abandon all of that, that I trust completely in something I couldn\'t see or understand.

The first moments were the hardest. I felt exposed, vulnerable, completely at the mercy of forces I couldn\'t control. But as I began to relax into the experience, I discovered something remarkable - the trust itself was liberating.

By letting go of my need to control and understand, I was able to experience things I never would have discovered otherwise. The trust exercise opened doors I didn\'t know existed and showed me possibilities I never would have imagined.

The experience was transformative. I learned that trust is not about giving up control - it\'s about finding a different kind of strength, the strength that comes from being open to whatever life has to offer.

When the trust exercise was complete, I felt like a different person. I had discovered a new way of being in the world, a way that was more open, more receptive, more trusting. The vulnerability had become my strength.

The trust exercise had taught me that sometimes the greatest courage comes from being willing to be vulnerable, and that the most profound experiences often require us to let go of what we think we know and trust in what we can\'t see.',
            ],
            [
                'title' => 'The Community Connection',
                'summary' => 'Finding belonging and support within the community of users.',
                'content' => 'When I first joined the community, I felt like an outsider. Everyone seemed to know each other, to understand the culture and the expectations, to have established relationships and connections. I was alone in a sea of familiar faces.

But as I began to participate in community discussions and share my own experiences, I started to feel a sense of belonging. People were welcoming and supportive, offering advice and encouragement without judgment. They shared their own struggles and triumphs, making me feel less alone in my journey.

The community became a source of strength and inspiration. When I was struggling with a difficult task, I could turn to others for support. When I achieved something meaningful, I could share my success with people who understood and appreciated the significance of my accomplishment.

The connections I formed within the community were deep and meaningful. These were people who understood my experiences, who shared my interests, who supported my growth and celebrated my successes. They became more than just fellow users - they became friends, mentors, and sources of inspiration.

The community connection had transformed my entire experience. I was no longer navigating this journey alone - I was part of a supportive network of people who were all working toward similar goals and supporting each other along the way.

The community had taught me that we are stronger together than we are alone, and that the connections we form with others who share our interests and experiences can be some of the most meaningful relationships we ever develop.',
            ],
            [
                'title' => 'The Breakthrough Moment',
                'summary' => 'A pivotal moment when everything clicked and understanding was achieved.',
                'content' => 'I had been struggling with a particular concept for weeks. No matter how hard I tried, I couldn\'t seem to grasp the underlying principles or understand how to apply them effectively. I was frustrated and beginning to doubt my abilities.

Then came the breakthrough moment. I was working on a seemingly unrelated task when suddenly, everything clicked. The pieces fell into place, the connections became clear, and I understood what had been eluding me for so long.

The moment was electric, transformative, life-changing. It was as if a light had been switched on in a dark room, illuminating everything that had been hidden in the shadows. I could see the patterns, understand the logic, and feel the rightness of the approach.

The breakthrough didn\'t just solve the immediate problem - it opened up new possibilities, new ways of thinking, new approaches to challenges I had never considered before. It was a moment of profound understanding that changed everything.

From that moment forward, my entire approach to the experience was different. I had gained not just knowledge, but wisdom. I had developed not just skills, but understanding. The breakthrough had been a turning point in my journey.

The breakthrough moment had taught me that sometimes understanding comes when we least expect it, and that the most profound insights often arrive after periods of struggle and confusion. The breakthrough had been worth every moment of frustration that preceded it.',
            ],
            [
                'title' => 'The Seasonal Cycle',
                'summary' => 'Experiencing the same type of task across different seasons and the insights gained.',
                'content' => 'I was surprised when I received a task that was similar to one I had completed months before, but with a seasonal twist. The basic structure was the same, but the context and the experience were completely different. It was like meeting an old friend in a new place.

The first time I had completed this type of task, it had been spring. The world was awakening, full of new life and possibility. The experience had been fresh and exciting, full of the energy of new beginnings.

This time, it was autumn. The world was preparing for rest, full of the beauty of change and transition. The same task felt different, more contemplative, more reflective. I was different too, more experienced, more confident, more aware.

The seasonal cycle taught me that the same experience can feel completely different depending on the context and the person experiencing it. The task hadn\'t changed, but I had, and the world around me had, and those changes transformed the entire experience.

As I completed the task in its new seasonal context, I found myself appreciating aspects of it that I had missed before. The seasonal cycle had given me a new perspective on an old experience, and that perspective was incredibly valuable.

The seasonal cycle had taught me that growth and change are ongoing processes, and that revisiting familiar experiences with new awareness can lead to deeper understanding and appreciation. The cycle had been a gift, a chance to see how far I had come and how much I had grown.',
            ],
            [
                'title' => 'The Final Lesson',
                'summary' => 'The culmination of a long journey and the wisdom gained along the way.',
                'content' => 'After years of experiences, challenges, and growth, I found myself facing what felt like a final lesson. It wasn\'t the end of my journey, but it was a moment of reflection and integration, a time to bring together everything I had learned.

The final lesson was not a single task, but a series of experiences designed to help me understand and appreciate the full scope of my journey. It was a time to reflect on where I had started, how far I had come, and what I had learned along the way.

As I worked through the final lesson, I was amazed by how much I had grown and changed. The person I was now was so different from the person I had been when I started this journey. I had developed new skills, new perspectives, new ways of being in the world.

The final lesson was also a time of gratitude. I was grateful for the challenges that had pushed me to grow, for the support that had helped me through difficult times, for the experiences that had taught me about myself and about life.

But most of all, the final lesson was a time of understanding. I finally understood that the journey itself was the destination, that the growth and learning were the real rewards, and that every experience, whether easy or difficult, had contributed to who I had become.

The final lesson had taught me that wisdom comes not from reaching a destination, but from the journey itself, and that the most profound lessons are often the ones we learn about ourselves along the way.',
            ],
            [
                'title' => 'The New Beginning',
                'summary' => 'Starting a new chapter with fresh perspective and renewed enthusiasm.',
                'content' => 'After completing what felt like a major chapter in my journey, I found myself at a new beginning. It was a strange feeling - part satisfaction at what I had accomplished, part excitement about what lay ahead, part uncertainty about the unknown future.

The new beginning was not about starting over, but about starting fresh. I was bringing with me all the knowledge, skills, and wisdom I had gained, but I was approaching the next phase of my journey with renewed enthusiasm and a clearer sense of purpose.

The first task of this new chapter felt different from any I had experienced before. I was more confident, more aware, more prepared, but I was also more open to new possibilities and new ways of experiencing the journey.

The new beginning was a time of integration and application. I was taking everything I had learned and applying it to new challenges, new experiences, new opportunities for growth. It was exciting to see how my previous experiences had prepared me for what lay ahead.

As I began this new chapter, I felt a sense of gratitude for the journey that had brought me to this point. Every challenge, every success, every moment of growth had prepared me for this new beginning, and I was ready to embrace whatever lay ahead.

The new beginning had taught me that endings are often just new beginnings in disguise, and that the wisdom we gain from our experiences becomes the foundation for whatever comes next. The new beginning was not just a fresh start - it was a continuation of a journey that had transformed me in ways I never could have imagined.',
            ],
            [
                'title' => 'The Unexpected Teacher',
                'summary' => 'Learning valuable lessons from an unlikely source.',
                'content' => 'I never expected to learn so much from someone I initially dismissed as unimportant. The unexpected teacher was someone I had barely noticed, someone who seemed to be on the periphery of the community, someone who didn\'t fit my preconceived notions of what a mentor should be.

But as I began to pay attention to their contributions, I realized that they had insights and wisdom that I had been missing. They approached challenges differently, saw possibilities I couldn\'t see, and had a perspective that was completely different from my own.

The unexpected teacher taught me to look beyond appearances, to listen to voices that might not be the loudest or most obvious, and to recognize that wisdom can come from the most unexpected sources. They showed me that sometimes the most valuable lessons come from people we least expect to teach us.

Through their guidance, I began to see my own experiences in a new light. They helped me understand aspects of my journey that had been unclear, and they offered perspectives that challenged my assumptions and expanded my understanding.

The unexpected teacher had become one of the most important influences in my journey, not because they had all the answers, but because they helped me ask better questions and see new possibilities.

The unexpected teacher had taught me that learning is not just about finding the right answers, but about asking the right questions, and that sometimes the most profound insights come from the most unexpected sources.',
            ],
            [
                'title' => 'The Gentle Challenge',
                'summary' => 'A task that pushed boundaries while maintaining comfort and safety.',
                'content' => 'The gentle challenge was exactly what I needed at a time when I was feeling overwhelmed and uncertain. It was designed to push me beyond my comfort zone, but in a way that felt safe and supportive rather than threatening or demanding.

The task required me to explore new territory, but it did so gradually, allowing me to acclimate to each new level of challenge before moving on to the next. It was like being guided up a mountain by a patient and experienced guide who knew exactly when to push and when to pause.

The gentle challenge taught me that growth doesn\'t have to be painful or traumatic. It showed me that it\'s possible to push boundaries while maintaining a sense of safety and comfort, and that the most effective challenges are often the ones that feel supportive rather than threatening.

As I worked through the gentle challenge, I found myself growing in confidence and capability. The gradual progression allowed me to build on each success, creating a foundation of confidence that supported me as I faced new challenges.

The gentle challenge had been a perfect example of how to approach growth and development. It had shown me that the most effective way to expand our capabilities is often through gentle, supportive challenges that respect our boundaries while encouraging us to explore new possibilities.

The gentle challenge had taught me that strength comes not from being pushed beyond our limits, but from being supported as we gradually expand those limits, and that the most meaningful growth often happens in an environment of safety and support.',
            ],
            [
                'title' => 'The Shared Experience',
                'summary' => 'A task completed with a partner and the deeper connection that resulted.',
                'content' => 'The shared experience was unlike anything I had done before. Instead of working alone, I was paired with a partner, and together we had to navigate a complex task that required cooperation, communication, and mutual trust.

At first, the partnership felt awkward and uncertain. We were strangers, brought together by circumstance, and we had to figure out how to work together effectively. But as we began to communicate and collaborate, something remarkable happened.

The shared experience created a bond between us that went beyond the task itself. We were not just working together - we were sharing something intimate and meaningful, something that required vulnerability and trust on both our parts.

The task required us to be completely honest with each other, to share our thoughts and feelings, to support each other through difficult moments. It was challenging, but it was also incredibly rewarding.

By the time we completed the shared experience, we had formed a connection that was deeper and more meaningful than most of the relationships in my life. We had shared something profound, and that sharing had created a bond that would last long after the task was complete.

The shared experience had taught me that the most meaningful connections often come from shared challenges, and that working together toward a common goal can create bonds that are stronger and more lasting than almost anything else.',
            ],
            [
                'title' => 'The Transformation',
                'summary' => 'A story about how the entire journey changed a person\'s life and perspective.',
                'content' => 'Looking back on my journey, I can see that it has been nothing short of a transformation. The person I am today is so different from the person I was when I started, and the changes go far beyond the specific skills and experiences I\'ve gained.

The transformation has been profound and multifaceted. I\'ve grown in confidence, in self-awareness, in my ability to connect with others and with myself. I\'ve developed new ways of thinking, new approaches to challenges, new perspectives on life and relationships.

But perhaps the most significant transformation has been in my understanding of myself. I\'ve discovered aspects of my personality that I never knew existed, capabilities that I never knew I possessed, and desires that I never knew I had. The journey has been a process of self-discovery and self-acceptance.

The transformation has also affected my relationships with others. I\'m more open, more honest, more willing to be vulnerable. I\'ve learned to communicate more effectively, to listen more deeply, to connect more authentically with the people in my life.

The transformation has been gradual but steady, like the changing of seasons or the growth of a tree. Each experience, each challenge, each moment of growth has contributed to the person I\'ve become, and I\'m grateful for every step of the journey.

The transformation has taught me that change is not just possible, but inevitable when we\'re open to growth and learning. It has shown me that the most profound transformations often come from the most unexpected experiences, and that the journey of self-discovery is one of the most rewarding adventures we can undertake.',
            ],
        ];

        foreach ($stories as $storyData) {
            $story = Story::create([
                'title' => $storyData['title'],
                'summary' => $storyData['summary'],
                'content' => $storyData['content'],
                'user_id' => $users->random()->id,
                'status' => ContentStatus::Approved->value,
                'is_premium' => rand(0, 10) < 2, // 20% chance of being premium
                'view_count' => rand(0, 2000),
                'report_count' => 0,
            ]);

            // Attach random tags to the story (1-4 tags per story)
            if ($availableTags->isNotEmpty()) {
                $tagCount = rand(1, min(4, $availableTags->count()));
                $randomTags = $availableTags->random($tagCount);
                $story->syncTags($randomTags);
            }
        }

        $this->command->info('Created ' . count($stories) . ' stories successfully.');
    }
}