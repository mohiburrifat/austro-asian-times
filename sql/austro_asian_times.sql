-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2025 at 07:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `austro_asian_times`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `article_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('draft','pending','approved','declined') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `author_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`article_id`, `title`, `content`, `image_url`, `status`, `created_at`, `updated_at`, `author_id`) VALUES
(1, 'Website Development', 'Website development is the process of creating and maintaining websites. It\'s a broad term that encompasses a variety of tasks, from the initial planning and design to the actual coding and deployment of a website. Think of it as constructing a house: you need a blueprint, materials, and skilled builders to bring it to life. Similarly, website development requires a well-defined plan, the right technologies, and the expertise of developers.\r\n\r\nAt its core, website development involves two main aspects:\r\n\r\nFront-end development: This is what users see and interact with directly. It\'s the visual and interactive layer of the website. Front-end developers use languages like HTML (for structure), CSS (for styling), and JavaScript (for interactivity) to create the layout, design, and user experience. Imagine the walls, paint, furniture, and how you move around inside the house – that\'s the front-end.\r\n\r\nBack-end development: This is the engine that powers the website behind the scenes. It deals with the server, database, and application logic. Back-end developers use languages like Python, Java, PHP, Ruby, and databases like MySQL, PostgreSQL, and MongoDB to manage data, handle user requests, and ensure the website functions correctly. Think of the plumbing, electrical wiring, and foundation of the house – essential for its operation but not directly visible.', 'uploads/img_68262211b0914.jfif', 'approved', '2025-05-15 17:19:13', '2025-05-16 14:00:28', 3),
(3, 'Machine Learning: The Intelligent Force', 'From personalized recommendations on your favorite streaming service to the sophisticated algorithms powering self-driving cars, machine learning (ML) has rapidly moved from the realm of science fiction to an integral part of our daily lives. This powerful branch of artificial intelligence is enabling computers to learn from data, identify patterns, and make decisions with increasing accuracy and autonomy, fundamentally reshaping industries and the way we interact with technology.\r\n\r\nAt its core, machine learning is about teaching computers without explicitly programming them for every single task. Instead, ML algorithms are fed vast amounts of data, allowing them to identify underlying structures, learn from examples, and improve their performance over time. Think of it like teaching a child – you don\'t provide a rule for every possible situation, but rather offer examples and feedback, allowing the child to develop their own understanding.\r\n\r\nThis data-driven approach has led to breakthroughs in numerous fields. In healthcare, ML algorithms are being used to analyze medical images for early disease detection, personalize treatment plans, and even accelerate drug discovery. The financial sector leverages ML for fraud detection, risk assessment, and algorithmic trading. Retail benefits from personalized marketing and optimized supply chains, while manufacturing employs ML for predictive maintenance and quality control.\r\n\r\nOne of the most visible applications lies in natural language processing (NLP), a subfield of ML that enables computers to understand and process human language. This powers virtual assistants like Siri and Alexa, improves machine translation, and enhances customer service through chatbots. Similarly, computer vision, another ML domain, allows machines to \"see\" and interpret images, driving advancements in facial recognition, autonomous vehicles, and robotic automation.\r\n\r\nHowever, the rise of machine learning also presents challenges. Concerns surrounding data privacy, algorithmic bias, and the potential impact on the job market are crucial considerations that require careful attention and ethical frameworks. Ensuring fairness, transparency, and accountability in ML systems is paramount to fostering public trust and maximizing the benefits of this technology for all.\r\n\r\nLooking ahead, the potential of machine learning appears limitless. As computational power continues to grow and data availability expands, we can expect even more sophisticated and impactful applications. From creating truly personalized learning experiences to tackling complex global challenges like climate change and disease, machine learning is poised to be a driving force of innovation in the years to come. Understanding its capabilities and limitations is no longer just for tech experts; it\'s becoming essential knowledge for navigating the increasingly intelligent world around us.', 'uploads/img_682744cb24c11.jpeg', 'approved', '2025-05-16 13:59:39', '2025-05-29 15:49:30', 3),
(4, 'World War II: A Global Conflict That Reshaped the World', 'World War II, a global conflict that raged from 1939 to 1945, remains the deadliest war in human history, claiming the lives of an estimated 60 to 80 million people. It was a war fought on land, sea, and air, involving a vast majority of the world\'s countries, ultimately reshaping the geopolitical landscape and leaving an indelible mark on the 20th century and beyond.\r\n\r\nThe Seeds of War: The end of World War I left a legacy of unresolved issues and simmering tensions. The Treaty of Versailles, imposed on Germany, was perceived as harsh and unjust, fostering resentment and contributing to economic instability. The rise of aggressive ideologies like fascism in Italy under Mussolini and Nazism in Germany under Hitler, coupled with Japan\'s expansionist ambitions in Asia, further destabilized the international order. The failure of the League of Nations to effectively maintain peace also played a significant role.\r\n\r\n\r\n\r\n\r\nKey Events and Turning Points: The invasion of Poland by Nazi Germany on September 1, 1939, is widely considered the start of World War II. This act of aggression prompted Britain and France to declare war on Germany. The early years of the war saw Germany\'s Blitzkrieg tactics result in the swift conquest of much of Europe, including France. The Battle of Britain in 1940 marked a crucial turning point as the Royal Air Force successfully defended against German air attacks, preventing a planned invasion.\r\n\r\nThe war expanded dramatically in 1941 with Germany\'s invasion of the Soviet Union (Operation Barbarossa) and Japan\'s attack on Pearl Harbor, which brought the United States into the conflict. These events created two major theaters of war: the European theater, primarily pitting the Allied powers (including Britain, the Soviet Union, and the United States) against the Axis powers (Germany and Italy), and the Pacific theater, where the United States and its allies fought against Japan.\r\n\r\nSignificant battles and campaigns shaped the course of the war. In Europe, the Battle of Stalingrad marked a major turning point on the Eastern Front, halting the German advance and leading to their eventual retreat. The Allied invasion of Normandy on D-Day in June 1944 opened a crucial second front in Western Europe, leading to the liberation of France and the push towards Germany. In the Pacific, battles like Midway and Guadalcanal were pivotal in turning the tide against Japan.\r\n\r\nThe Holocaust and Other Atrocities: World War II was also marked by horrific atrocities, most notably the Holocaust, the systematic genocide of approximately six million European Jews by Nazi Germany and its collaborators. Other groups, including Roma, homosexuals, and disabled people, were also persecuted and murdered. In Asia, the Japanese military committed numerous war crimes, including the Nanking Massacre and the use of biological weapons.\r\n\r\n\r\nThe End of the War and Its Aftermath: The war in Europe concluded with the unconditional surrender of Germany in May 1945, following the Soviet capture of Berlin and Hitler\'s suicide. The war in the Pacific ended with Japan\'s surrender in September 1945, after the United States dropped atomic bombs on Hiroshima and Nagasaki.', 'uploads/img_682745dbe6148.jpg', 'approved', '2025-05-16 14:04:11', '2025-05-16 16:59:06', 5),
(5, 'Climate Change: A Planetary Crisis Demanding Urgent Action', 'The Earth\'s climate is changing at an unprecedented rate, driven by human activities that are releasing greenhouse gases into the atmosphere. This phenomenon, known as climate change, is no longer a distant threat; it is a present reality with far-reaching consequences for our planet and all life on it. From rising sea levels and extreme weather events to disruptions in ecosystems and threats to human health, the evidence is irrefutable, and the need for urgent action has never been more critical.\r\n\r\n\r\n\r\nThe Science is Clear: The overwhelming scientific consensus, supported by decades of research from institutions worldwide, points to the burning of fossil fuels (coal, oil, and natural gas) as the primary driver of this change. These activities release carbon dioxide (CO \r\n2\r\n​\r\n ) and other greenhouse gases, such as methane (CH \r\n4\r\n​\r\n ) and nitrous oxide (N \r\n2\r\n​\r\n O), into the atmosphere. These gases trap heat, leading to a gradual warming of the planet. Deforestation, industrial processes, and intensive agriculture also contribute significantly to greenhouse gas emissions.\r\n\r\n\r\n\r\n\r\nObserved Impacts: The effects of climate change are already being felt across the globe:\r\n\r\nRising Global Temperatures: The average global temperature has increased significantly over the past century, and the last decade has been the warmest on record. This warming trend is causing heatwaves, longer and hotter summers, and disruptions to natural cycles.\r\n\r\nMelting Ice and Rising Sea Levels: Polar ice caps and glaciers are melting at an alarming rate, contributing to a rise in global sea levels. This threatens coastal communities with increased flooding, erosion, and displacement. Low-lying island nations are particularly vulnerable.\r\n\r\n\r\nExtreme Weather Events: Climate change is intensifying extreme weather events. We are witnessing more frequent and severe heatwaves, droughts, wildfires, floods, and powerful storms. These events cause widespread devastation, loss of life, and significant economic damage. For instance, the increased intensity of cyclones in coastal regions like Bangladesh is a stark reminder of these impacts.\r\n\r\n\r\nOcean Acidification: The absorption of excess CO \r\n2\r\n​\r\n  by the oceans is causing them to become more acidic. This poses a significant threat to marine ecosystems, particularly coral reefs and shellfish, which are vital for ocean biodiversity and food security.\r\n\r\nDisruption of Ecosystems: Changes in temperature and precipitation patterns are disrupting ecosystems worldwide. Species are being forced to migrate, face extinction, or experience changes in their behavior and interactions. This loss of biodiversity weakens the resilience of ecosystems and can have cascading effects.\r\n\r\n\r\nThreats to Human Health: Climate change has direct and indirect impacts on human health. Heatwaves can lead to heatstroke and other heat-related illnesses. Changes in vector-borne disease distribution, increased respiratory problems due to air pollution linked to fossil fuels and wildfires, and food and water insecurity are all exacerbated by climate change.\r\n\r\nThe Path Forward: Mitigation and Adaptation: Addressing climate change requires a two-pronged approach:\r\n\r\nMitigation: This involves reducing greenhouse gas emissions. The most crucial step is the transition away from fossil fuels towards renewable energy sources like solar, wind, and hydro power. Improving energy efficiency, promoting sustainable transportation, adopting sustainable agricultural practices, and preventing deforestation are also essential mitigation strategies. International agreements, such as the Paris Agreement, aim to set targets and frameworks for global emissions reductions.\r\n\r\nAdaptation: Even with ambitious mitigation efforts, some level of climate change is already locked in. Adaptation involves taking steps to prepare for and adjust to the current and future impacts of climate change. This includes building seawalls, developing drought-resistant crops, improving water management systems, and strengthening public health infrastructure to deal with climate-related health risks. For a country like Bangladesh, adaptation strategies are critical due to its vulnerability to sea-level rise and extreme weather events.\r\nThe Urgency of Action: The window of opportunity to limit the most severe impacts of climate change is rapidly closing. Delaying action will only lead to more significant and irreversible consequences, increasing costs and suffering in the future. A global, coordinated effort involving governments, businesses, communities, and individuals is essential to transition to a sustainable and resilient future', 'uploads/img_68274d08a8a70.jpeg', 'approved', '2025-05-16 14:34:48', '2025-05-29 15:49:28', 6),
(6, 'What is machine learning?', 'Since deep learning and machine learning tend to be used interchangeably, it’s worth noting the nuances between the two. Machine learning, deep learning, and neural networks are all sub-fields of artificial intelligence. However, neural networks is actually a sub-field of machine learning, and deep learning is a sub-field of neural networks.\r\n\r\nThe way in which deep learning and machine learning differ is in how each algorithm learns. \"Deep\" machine learning can use labeled datasets, also known as supervised learning, to inform its algorithm, but it doesn’t necessarily require a labeled dataset. The deep learning process can ingest unstructured data in its raw form (e.g., text or images), and it can automatically determine the set of features which distinguish different categories of data from one another. This eliminates some of the human intervention required and enables the use of large amounts of data. You can think of deep learning as \"scalable machine learning\" as Lex Fridman notes in this MIT lecture1.\r\n\r\nClassical, or \"non-deep,\" machine learning is more dependent on human intervention to learn. Human experts determine the set of features to understand the differences between data inputs, usually requiring more structured data to learn.\r\n\r\nNeural networks, or artificial neural networks (ANNs), are comprised of node layers, containing an input layer, one or more hidden layers, and an output layer. Each node, or artificial neuron, connects to another and has an associated weight and threshold. If the output of any individual node is above the specified threshold value, that node is activated, sending data to the next layer of the network. Otherwise, no data is passed along to the next layer of the network by that node. The “deep” in deep learning is just referring to the number of layers in a neural network. A neural network that consists of more than three layers, which would be inclusive of the input and the output can be considered a deep learning algorithm or a deep neural network. A neural network that only has three layers is just a basic neural network.', 'uploads/img_682777ec2a41f.jfif', 'approved', '2025-05-16 17:37:48', '2025-05-29 15:41:24', 3),
(7, 'My Hobby', 'My hobby is drawing.', 'uploads/img_683881e5e3646.png', 'approved', '2025-05-29 15:48:53', '2025-05-29 15:49:25', 7);

-- --------------------------------------------------------

--
-- Table structure for table `article_tags`
--

CREATE TABLE `article_tags` (
  `article_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `article_tags`
--

INSERT INTO `article_tags` (`article_id`, `tag_id`) VALUES
(1, 1),
(3, 1),
(4, 2),
(4, 3),
(5, 4),
(5, 5),
(6, 1),
(7, 6),
(7, 7);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `article_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` text NOT NULL,
  `status` enum('pending','approved') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `article_id`, `content`, `created_at`, `name`, `status`) VALUES
(5, 4, 'very nice article', '2025-05-29 15:40:02', 'Reader 1', 'approved'),
(6, 6, 'good', '2025-05-29 15:45:57', 'Reader 2', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `comment_settings`
--

CREATE TABLE `comment_settings` (
  `article_id` int(11) NOT NULL,
  `comments_enabled` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment_settings`
--

INSERT INTO `comment_settings` (`article_id`, `comments_enabled`) VALUES
(1, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`tag_id`, `name`) VALUES
(5, 'Climate Change'),
(3, 'Germany'),
(6, 'hobby'),
(7, 'my'),
(2, 'Politics'),
(1, 'Tech'),
(4, 'Weather');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('journalist','editor') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `Is_active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `role`, `created_at`, `Is_active`) VALUES
(2, 'editor', 'editor@gmail.com', '$2y$10$8d9RtAJ0IZI7i89AD00t1utuCahb4Nxx4SWh1Pa.kWzFv8hOnUA8C', 'editor', '2025-05-14 06:09:40', 0),
(3, 'journalist1', 'journalist1@gmail.com', '$2y$10$JDjXiTm.G6fM/0To6zbZdOOWXdhMfzn5TY1OLhSUS6A.6guKGYCvm', 'journalist', '2025-05-15 16:34:00', 0),
(5, 'journalist2', 'Journalist2@gmail.com', '$2y$10$c.y98tOBXgh8bFuDPSXCu.28YzySvedfcWjI4moO2DzgEzzh9Kdl6', 'journalist', '2025-05-16 14:01:20', 0),
(6, 'journalist 3', 'journalist3@gmail.com', '$2y$10$G37IfkXka1oZNigaV1UDUuIqig9BE6UzB69WAVAmd076/zVoETWTi', 'journalist', '2025-05-16 14:32:07', 0),
(7, 'journalist4', 'journalist4@gmail.com', '$2y$10$SIEY62YoWaje7xgMB16roeVLAlDBZt0/N4pX3zbu.tx4hIjWdW5ja', 'journalist', '2025-05-29 15:42:53', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`article_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `article_tags`
--
ALTER TABLE `article_tags`
  ADD PRIMARY KEY (`article_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `article_id` (`article_id`);

--
-- Indexes for table `comment_settings`
--
ALTER TABLE `comment_settings`
  ADD PRIMARY KEY (`article_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `article_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `article_tags`
--
ALTER TABLE `article_tags`
  ADD CONSTRAINT `article_tags_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`article_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`article_id`) ON DELETE CASCADE;

--
-- Constraints for table `comment_settings`
--
ALTER TABLE `comment_settings`
  ADD CONSTRAINT `comment_settings_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`article_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
