<?php
/**
 * EmoTe (Emotive Text) API
 * php port of Synesketch Emotion packages by Jesse Fulton - http://jessefulton.com
 *
 * Synesketch 
 * Copyright (C) 2008  Uros Krcadinac
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */



/**
 * Defines logic for transfering textual affect information -- emotional
 * manifestations recognised in text -- into visual output.
 * 
 * @author Uros Krcadinac email: uros@krcadinac.com
 * @version 1.0
 */
class Eklekt_Emotion_Empathyscope {

	private static $instance;

	private $lexUtil;

	private function __construct() {
		$this->lexUtil = Eklekt_Emotion_Utility_Lexical::getInstance();
	}

	/**
	 * Returns the Singleton instance of the {@link Empathyscope}.
	 * 
	 * @return Eklekt_Emotion_Empathyscope instance
	 * @throws IOException
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new Eklekt_Emotion_Empathyscope();
		}
		return self::$instance;
	}

	/**
	 * Textual affect sensing behavior, the main NLP alghoritm which uses
	 * Synesketch Lexicon and several heuristic rules.
	 * 
	 * @param text
	 *            String representing the text to be analysed
	 * @return {@link EmotionalState} which represents data recognised from the
	 *         text
	 * @throws IOException
	 */
	public function feel($text) {

		$text = str_replace('\n', ' ', $text);
		$affectWords = array();
		//$sentences = ParsingUtility.parseSentences(text);
		$sentences = preg_split('/[!\.]+/', $text);

		foreach ($sentences as $sentence) {

			// we imploy 5 heuristic rules to adjust emotive weights of the
			// words:
			// (1) negation in a sentence => flip valence of the affect words in
			// it
			$hasNegation = Eklekt_Emotion_Utility_Heuristics::hasNegation(strtolower($sentence));


			// (2) more exclamination signs in a sentence => more intensive
			// emotive weights
			$exclaminationQoef = Eklekt_Emotion_Utility_Heuristics::computeExclaminationQoef(strtolower($sentence));


			$splittedWords = explode(" ", $sentence); 
			$previousWord = "";
			foreach ($splittedWords as $splittedWord) {

				$emoWord = $this->lexUtil->getEmoticonAffectWord($splittedWord);

				if ($emoWord != null) {
					// (3) more emoticons with more 'emotive' signs (e.g. :DDDD)
					// => more intensive emotive weights
					$emoticonCoef = Eklekt_Emotion_Utility_Heuristics::computeEmoticonCoef($splittedWord, $emoWord);
					$emoWord->adjustWeights($exclaminationQoef * $emoticonCoef);
					$affectWords[] = $emoWord;
				} else {
				    $words = preg_split('/[^A-Za-z\-]+/', $splittedWord);
				    if (empty($words)) {
				        $words = array($splittedWord);
				    }
	    	    
					foreach ($words as $word) {
					    
						$emoWord = $this->lexUtil->getAffectWord(strtolower($word));
						if ($emoWord != null) {

							// (4) word is upper case => more intensive emotive
							// weights
							$capsLockCoef = Eklekt_Emotion_Utility_Heuristics::computeCapsLockQoef($word);

							// (5) previous word is a intensity modifier (e.g.
							// "extremly") => more intensive emotive weights
							$modifierCoef = Eklekt_Emotion_Utility_Heuristics::computeModifier($previousWord);


							// change the affect word!
							if ($hasNegation)
								$emoWord->flipValence();
							
							$emoWord->adjustWeights($exclaminationQoef * $capsLockCoef * $modifierCoef);

							$affectWords[] = $emoWord;
						}
						$previousWord = $word;
					}
				}
			}
		}
		return $this->createEmotionalState($text, $affectWords);
	}

	private function createEmotionalState($text, $affectWords) {
	    
	    
	    //instead of creating a TreeSet, add to array, then sort by custom function
		//TreeSet<Emotion> emotions = new TreeSet<Emotion>();
		
	    $emotions = array();
	    
		$generalValence = 0;
		//$valence, $generalWeight, $happinessWeight, $sadnessWeight, $angerWeight, $fearWeight, $disgustWeight, $surpriseWeight;
		// valence = generalWeight = happinessWeight = sadnessWeight =
		// angerWeight = fearWeight = disgustWeight = surpriseWeight = 0.0;
		$valence = 0.0;
		$generalWeight = 0.0;
		$happinessWeight = 0.0;
		$sadnessWeight = 0.0;
		$angerWeight = 0.0;
		$fearWeight = 0.0;
		$disgustWeight = 0.0;
		$surpriseWeight = 0.0;

		// compute weights. maximum weights for the particular emotion are
		// taken.
		foreach ($affectWords as $affectWord) {
			$valence += $affectWord->getGeneralValence();
			if ($affectWord->getGeneralWeight() > $generalWeight)
				$generalWeight = $affectWord->getGeneralWeight();
			if ($affectWord->getHappinessWeight() > $happinessWeight)
				$happinessWeight = $affectWord->getHappinessWeight();
			if ($affectWord->getSadnessWeight() > $sadnessWeight)
				$sadnessWeight = $affectWord->getSadnessWeight();
			if ($affectWord->getAngerWeight() > $angerWeight)
				$angerWeight = $affectWord->getAngerWeight();
			if ($affectWord->getFearWeight() > $fearWeight)
				$fearWeight = $affectWord->getFearWeight();
			if ($affectWord->getDisgustWeight() > $disgustWeight)
				$disgustWeight = $affectWord->getDisgustWeight();
			if ($affectWord->getSurpriseWeight() > $surpriseWeight)
				$surpriseWeight = $affectWord->getSurpriseWeight();
		}
		if ($valence > 0)
			$generalValence = 1;
		else if ($valence < 0)
			$generalValence = -1;

		if ($happinessWeight > 0)
			$emotions[] = (new Eklekt_Emotion($happinessWeight, Eklekt_Emotion::HAPPINESS));
		if ($sadnessWeight > 0)
			$emotions[] = (new Eklekt_Emotion($sadnessWeight, Eklekt_Emotion::SADNESS));
		if ($angerWeight > 0)
			$emotions[] = (new Eklekt_Emotion($angerWeight, Eklekt_Emotion::ANGER));
		if ($fearWeight > 0)
			$emotions[] = (new Eklekt_Emotion($fearWeight, Eklekt_Emotion::FEAR));
		if ($disgustWeight > 0)
			$emotions[] = (new Eklekt_Emotion($disgustWeight, Eklekt_Emotion::DISGUST));
		if ($surpriseWeight > 0)
			$emotions[] = (new Eklekt_Emotion($surpriseWeight, Eklekt_Emotion::SURPRISE));
		if (empty($emotions))
			$emotions[] = (new Eklekt_Emotion((0.2 + $generalWeight) / 1.2, Eklekt_Emotion::NEUTRAL));
			
		//TODO: SORT $emotions so strongest emotions are at the front.	
		
		return new Eklekt_Emotion_EmotionalState($text, $emotions, $generalWeight, $generalValence);
	}

}
