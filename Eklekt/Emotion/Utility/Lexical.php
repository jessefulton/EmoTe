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
 * Utility class for some text processing alghoritms
 * 
 * @author Uros Krcadinac email: uros@krcadinac.com
 * @version 1.0
 */
class Eklekt_Emotion_Utility_Lexical {

	private static $instance;

	private $fileNameLexicon = "/Data/synesketch_lexicon.txt";
	private $fileNameEmoticons = "/Data/synesketch_lexicon_emoticons.txt";
	private $fileNameProperties = "/Data/keywords.xml";

	private $affectWords;
	private $emoticons;

	private $negations;

	private $intensityModifiers;

	private $normalisator = 0.75;

	private function __construct() {
		$this->affectWords = array();
		$this->emoticons = array();

		
		$intensityModifiers = "very, awfully, dreadfully, eminently, exceedingly, exceptionally, extra, extremely, greatly, highly, most, notably, absolutely, awfully, completely, deeply, eminently, emphatically, exceedingly, greatly, great, highly, high, hugely, huge, mighty, most, much, notably, remarkably, strikingly, surpassingly, terribly";
        $negations = "no, not, don't, dont, haven't, weren't, wasn't, didn't";		
		//TODO: TRANSLATE!
		$this->negations = explode(", ", $negations);
		$this->intensityModifiers = explode(", ", $intensityModifiers);
		$this->affectWords = $this->parseLexiconFile(realpath(dirname(__FILE__)) . $this->fileNameLexicon);
		$this->emoticons = $this->parseLexiconFile(realpath(dirname(__FILE__)) . $this->fileNameEmoticons);
	}

	/**
	 * Returns the Singleton instance of the {@link LexicalUtility}.
	 * 
	 * @return the instance of {@link LexicalUtility}
	 * @throws IOException
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new Eklekt_Emotion_Utility_Lexical();
		}
		return self::$instance;
	}

	
	
	//TODO: TRANSLATE!
	private function parseLexiconFile($fileName) {
		$wordList = array();
	    $lines = file($fileName);
		foreach ($lines as $line) {
			$record = $this->parseLine($line);
			$wordList[] = ($record);
		}
		return $wordList;
	}

	/**
	 * Parses one line of the Synesketch Lexicon and returns the
	 * {@link AffectWord}
	 * 
	 * @param line
	 *            {@link String} representing the line of the Synesketch Lexicon
	 * @return {@link AffectWord}
	 */
	private function parseLine($line) {
		$text = explode(" ", $line);
		$word = $text[0];
		$generalWeight = floatval($text[1]);
		$happinessWeight = floatval($text[2]);
		$sadnessWeight = floatval($text[3]);
		$angerWeight = floatval($text[4]);
		$fearWeight = floatval($text[5]);
		$disgustWeight = floatval($text[6]);
		$surpriseWeight = floatval($text[7]);
		$value = new Eklekt_Emotion_AffectWord($word, $generalWeight, $happinessWeight,
				$sadnessWeight, $angerWeight, $fearWeight, $disgustWeight,
				$surpriseWeight, $this->normalisator);
		return $value;
	}

	/**
	 * Returns the instance of {@link AffectWord} for the given word.
	 * 
	 * @param word
	 *            {@link String} representing the word
	 * @return {@link AffectWord}
	 */
	public function getAffectWord($word) {
		foreach ($this->affectWords as $affectWord) {
			if (strcasecmp($word, $affectWord->getWord()) == 0) {
				return clone($affectWord);
			}
		}
		return null;
	}

	/**
	 * Returns the instance of {@link AffectWord} for the given word, which is
	 * emoticon.
	 * 
	 * @param word
	 *            {@link String} representing the word
	 * @return {@link AffectWord}
	 */
	public function getEmoticonAffectWord($word) {
		foreach ($this->emoticons as $affectWordEmoticon) {
			if (strcasecmp($word, $affectWordEmoticon->getWord()) == 0) {
				return clone($affectWordEmoticon);
			}
		}
		foreach ($this->emoticons as $affectWordEmoticon) {
			$emoticon = $affectWordEmoticon->getWord();
			if (stripos($word, $emoticon) === 0) {
				$affectWordEmoticon->setStartsWithEmoticon(true);
				return clone($affectWordEmoticon);
			}
		}
		return null;
	}

	/**
	 * Returns all instances of {@link AffectWord} which represent emoticons for
	 * the given sentence.
	 * 
	 * @param sentence
	 *            {@link String} representing the sentence
	 * @return the list of {@link AffectWord} instances
	 */
	public function getEmoticonWords($sentence) {
		$value = array();
		foreach ($this->emoticons as $emoticon) {
			$emoticonWord = $emoticon->getWord();
			if (stripos($sentence, $emoticonWord) !== FALSE) {
				$emoticon->setStartsWithEmoticon(true);
				$value[] = $emoticon;
			}
		}
		return $value;
	}

	/**
	 * Returns all instances of {@link AffectWord}
	 * 
	 * @return the list of {@link AffectWord} instances
	 */
	public function getAffectWords() {
		return $this->affectWords;
	}

	/**
	 * Returns true if the word is a negation.
	 * 
	 * @param word
	 *            {@link String} which represents a word
	 * @return boolean, true is the word is a negation
	 */
	public function isNegation($word) {
		return in_array($word, $this->negations);
	}

	/**
	 * Returns true if the sentence contains a negation word in it.
	 * 
	 * @param sentence
	 *            {@link String} which represents a sentence
	 * @return boolean, true is the sentence contains negations
	 */
	public function hasNegation($sentence) {
		foreach ($this->negations as $negation) {
			if (stripos($sentence, $negation) !== FALSE) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns true if the word is an intensity modifier.
	 * 
	 * @param word
	 *            {@link String} which represents a word
	 * @return boolean, true is the word is an intensity modifier
	 */
	public function isIntensityModifier($word) {
		return in_array($word, $this->intensityModifiers);
	}

}
