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
 * Represents one unit from the Synesketch Lexicon: a word associated with
 * emotional meaning, and it's emotional weights and valence.
 * <p>
 * Synesketch Lexicon, which consits of several thousand words (with emoticons),
 * associates these atributes to a word:
 * <ul>
 * <li>General emotional weight
 * <li>General emotional valence
 * <li>Happiness weight
 * <li>Sadness weight
 * <li>Fear weight
 * <li>Anger weight
 * <li>Disgust weight
 * <li>Surprise weight
 * </ul>
 * 
 * @author Uros Krcadinac email: uros@krcadinac.com
 * @version 1.0
 */
class Eklekt_Emotion_AffectWord {

	private $word;

	private $generalWeight = 0.0;

	private $generalValence = 0.0;

	private $happinessWeight = 0.0;

	private $sadnessWeight = 0.0;

	private $angerWeight = 0.0;

	private $fearWeight = 0.0;

	private $disgustWeight = 0.0;

	private $surpriseWeight = 0.0;

	private $startsWithEmoticon = false;


	/**
	 * Class constructor which sets the affect word and it's weights. Valence is
	 * calculated as a function of different emotion type weights.
	 * 
	 * @param word
	 *            {@link String} representing the word
	 * @param generalWeight
	 *            double representing the general emotional weight
	 * @param $happinessWeight
	 *            double representing the happiness weight
	 * @param $sadnessWeight
	 *            double representing the sadness weight
	 * @param $angerWeight
	 *            double representing the anger weight
	 * @param $fearWeight
	 *            double representing the fear weight
	 * @param $disgustWeight
	 *            double representing the disgust weight
	 * @param $surpriseWeight
	 *            double representing the surprise weight
	 * @param quoficient
	 *            double representing the quoficient for adjusting the weights
	 */
	public function __construct($word, $generalWeight = 0.0,
			$happinessWeight = 0.0, $sadnessWeight = 0.0, $angerWeight = 0.0,
			$fearWeight = 0.0, $disgustWeight = 0.0, $surpriseWeight = 0.0
			, $quoficient = 1.0) {
		$this->word = $word;
		$this->generalWeight = $generalWeight;
		$this->happinessWeight = $happinessWeight;
		$this->sadnessWeight = $sadnessWeight;
		$this->angerWeight = $angerWeight;
		$this->fearWeight = $fearWeight;
		$this->disgustWeight = $disgustWeight;
		$this->surpriseWeight = $surpriseWeight;
		$this->adjustWeights($quoficient);
		$this->generalValence = $this->getValenceSum();
	}

	
	/**
	 * Adjusts weights by the certain quoficient.
	 * 
	 * @param quoficient
	 *            double representing the quoficient for adjusting the weights
	 */

	public function adjustWeights($quoficient) {
		$this->generalWeight = $this->generalWeight * $quoficient;
		$this->happinessWeight = $this->happinessWeight * $quoficient;
		$this->sadnessWeight = $this->sadnessWeight * $quoficient;
		$this->angerWeight = $this->angerWeight * $quoficient;
		$this->fearWeight = $this->fearWeight * $quoficient;
		$this->disgustWeight = $this->disgustWeight * $quoficient;
		$this->surpriseWeight = $this->surpriseWeight * $quoficient;
		$this->normalise();
	}

	private function normalise() {
		if ($this->generalWeight > 1)
			$this->generalWeight = 1.0;
		if ($this->happinessWeight > 1)
			$this->happinessWeight = 1.0;
		if ($this->sadnessWeight > 1)
			$this->sadnessWeight = 1.0;
		if ($this->angerWeight > 1)
			$this->angerWeight = 1.0;
		if ($this->fearWeight > 1)
			$this->fearWeight = 1.0;
		if ($this->disgustWeight > 1)
			$this->disgustWeight = 1.0;
		if ($this->surpriseWeight > 1)
			$this->surpriseWeight = 1.0;
	}

	/**
	 * Flips valence of the word -- calculates change from postive to negative
	 * emotion.
	 */
	public function flipValence() {
		$generalValence = $this->generalValence * (-1);
		$temp = $this->happinessWeight;
		$this->happinessWeight = max(max($this->sadnessWeight, $this->angerWeight), max($this->fearWeight, $this->disgustWeight));
		$this->sadnessWeight = $temp;
		$this->angerWeight = $temp / 2;
		$this->fearWeight = $temp / 2;
		$this->disgustWeight = $temp / 2;
	}

	/**
	 * Makes duplicate of the object.
	 * 
	 * @return {@link AffectWord}, new duplicated object
	 */
	public function __clone() {
		$value = new Eklekt_Emotion_AffectWord($this->word, $this->generalWeight, $this->happinessWeight,
				$this->sadnessWeight, $this->angerWeight, $this->fearWeight, $this->disgustWeight,
				$this->surpriseWeight);
		$value->setStartsWithEmoticon($this->startsWithEmoticon);
		return $value;
	}
	
	
	
	/**
	 * Returns true if the word starts with the emoticon.
	 * 
	 * @return boolean (true if the word starts with the emoticon, false if not)
	 */

	public function startsWithEmoticon() {
		return $this->startsWithEmoticon;
	}

	/**
	 * Sets does the word start with emoticon.
	 * 
	 * @param startsWithEmoticon
	 *            boolean (true if the word starts with the emoticon, false if
	 *            not)
	 */
	public function setStartsWithEmoticon($startsWithEmoticon) {
		$this->startsWithEmoticon = $startsWithEmoticon;
	}

	/**
	 * Getter for the anger weight.
	 * 
	 * @return double which represents the anger weight
	 */
	public function getAngerWeight() {
		return $this->angerWeight;
	}

	/**
	 * Getter for the anger weight.
	 * 
	 * @param $angerWeight
	 *            double which represents the anger weight
	 */
	public function setAngerWeight($angerWeight) {
		$this->angerWeight = $angerWeight;
	}

	/**
	 * Getter for the disgust weight.
	 * 
	 * @return double which represents the disgust weight
	 */
	public function getDisgustWeight() {
		return $this->disgustWeight;
	}

	/**
	 * Setter for the disgust weight.
	 * 
	 * @param $disgustWeight
	 *            double which represents the disgust weight
	 */
	public function setDisgustWeight($disgustWeight) {
		$this->disgustWeight = $disgustWeight;
	}

	/**
	 * Getter for the fear weight.
	 * 
	 * @return double which represents the fear weight
	 */
	public function getFearWeight() {
		return $this->fearWeight;
	}

	/**
	 * Getter for the fear weight.
	 * 
	 * @param $fearWeight
	 *            double which represents the fear weight
	 */
	public function setFearWeight($fearWeight) {
		$this->fearWeight = $fearWeight;
	}

	/**
	 * Getter for the happiness weight.
	 * 
	 * @return double which represents the happiness weight
	 */
	public function getHappinessWeight() {
		return $this->happinessWeight;
	}

	/**
	 * Setter for the happiness weight.
	 * 
	 * @param $happinessWeight
	 *            double which represents the happiness weight
	 */
	public function setHappinessWeight($happinessWeight) {
		$this->happinessWeight = $happinessWeight;
	}

	/**
	 * Getter for the sadness weight.
	 * 
	 * @return double which represents the sadness weight
	 */
	public function getSadnessWeight() {
		return $this->sadnessWeight;
	}

	/**
	 * Setter for the sadness weight.
	 * 
	 * @param $sadnessWeight
	 *            double which represents the sadness weight
	 */
	public function setSadnessWeight($sadnessWeight) {
		$this->sadnessWeight = $sadnessWeight;
	}

	/**
	 * Getter for the surprise weight.
	 * 
	 * @return double which represents the surprise weight
	 */
	public function getSurpriseWeight() {
		return $this->surpriseWeight;
	}

	/**
	 * Setter for the surprise weight.
	 * 
	 * @param $surpriseWeight
	 *            double which represents the surprise weight
	 */
	public function setSurpriseWeight($surpriseWeight) {
		$this->surpriseWeight = $surpriseWeight;
	}

	/**
	 * Getter for the word.
	 * 
	 * @return {@link String} which represents the word
	 */
	public function getWord() {
		return $this->word;
	}

	/**
	 * Getter for the general weight.
	 * 
	 * @return double which represents the general weight
	 */
	public function getGeneralWeight() {
		return $this->generalWeight;
	}

	/**
	 * Setter for the general weight.
	 * 
	 * @param generalWeight
	 *            double which represents the general weight
	 */
	public function setGeneralWeight($generalWeight) {
		$this->generalWeight = $generalWeight;
	}

	/**
	 * Getter for the general valence.
	 * 
	 * @return double which represents the general valence
	 */
	public function getGeneralValence() {
		return $this->generalValence;
	}

	/**
	 * Setter for the general valence
	 * 
	 * @param generalValence
	 *            double which represents the general valence
	 */
	public function setGeneralValence($generalValence) {
		$this->generalValence = $generalValence;
	}

	/**
	 * Sets the boolean value which determines does a word have specific
	 * emotional weight for emotion types defined by Ekman: happiness, sadness,
	 * fear, anger, disgust, and surprise.
	 * 
	 * @return boolean value, true if all specific emotional weight have the
	 *         value of zero
	 */
	public function isZeroEkman() {
		if ($this->getWeightSum() == 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Returns a string representation of the object.
	 * 
	 * @return a string representation of the object
	 */
	public function __toString() {
		return $this->word . " " . $this->generalWeight . " " . $this->happinessWeight . " "
				. $this->sadnessWeight . " " . $this->angerWeight . " " . $this->fearWeight . " "
				. $this->disgustWeight . " " . $this->surpriseWeight;
	}

	private function getValenceSum() {
		return $this->happinessWeight - $this->sadnessWeight - $this->angerWeight - $this->fearWeight
				- $this->disgustWeight;
	}

	private function getWeightSum() {
		return $this->happinessWeight + $this->sadnessWeight + $this->angerWeight + $this->fearWeight
				+ $this->disgustWeight + $this->surpriseWeight;
	}

}
