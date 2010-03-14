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
 * Defines emotional content of the text.
 * <p>
 * That is:
 * <ul>
 * <li>General emotional weight
 * <li>General valence (emotion is positive or negative)
 * <li>Six specific emotional weights, defined by Ekman's categories: happiness
 * weight, sadness weight, fear weight, anger weight, disgust weight, surprise
 * weight. These specific emotions are defined by the class {@link Emotion}.
 * <li>Previous {@link EmotionalState} (so that whole emotional history of one
 * conversation can be accessed from the Processing applet ({@link PApplet})).
 * </ul>
 * <p>
 * Weights have values between 0 and 1 (0 for no emotion, 1 for full emotion,
 * 0.5 for the emotion of average intesity). Valence can be -1, 0, or 1
 * (negative, neutral, and positive emotion, respectively).
 * 
 * @author Uros Krcadinac email: uros@krcadinac.com
 * @version 1.0
 * 
 */
class Eklekt_Emotion_EmotionalState {

	public $generalWeight = 0.0;

	public $valence = 0;

	public $previous;

	public $emotions;

	
	
	public $text;

	/**
	 * Class constructor that sets text which is to be synestheticaly
	 * interpreted
	 * 
	 * @param text
	 *            {@link String} representing the text which is to be synestheticaly
	 *            interpreted
	 */

	public function __construct($text = "", $emotions = null, $generalWeight=0.0, $valence=0) {
		$this->text = $text;
		if ($emotions) {
		    $this->emotions = $emotions;
		}
		else {
		    $this->emotions = array();
		    $this->emotions[] = new Eklekt_Emotion(1.0, Eklekt_Emotion::NEUTRAL);
		}
		$this->generalWeight = $generalWeight;
		$this->valence = $valence;
	}



	
	
	/**
	 * Getter for the text used as an interpretation resource
	 * 
	 * @return {@link String} representing the text
	 */

	public function getText() {
		return $this->text;
	}
	
	
	
	/**
	 * Returns {@link Emotion} with the highest weight.
	 * 
	 * @return Emotion with the highest weight
	 */

	public function getStrongestEmotion() {
		try {
		    return $this->emotions[0];   
		}
		catch (Exception $e) {
		    return null;
		}
	}

	
	
	/**
	 * Returns several emotions ({@link Emotion} instances) with the highest
	 * weight.
	 * 
	 * @param stop
	 *            int representing the number of emotions which is to searched
	 *            for
	 * @return list of emotions ({@link Emotion} instances) with the highest
	 *         weight
	 */
	public function getFirstStrongestEmotions($stop) {
		$value = array();
		foreach ($this->emotions as $e) {
			if ($stop <= 0) {
				break;
			}
			$value[] = $e;
			$stop--;
		}
		return $value;
	}

	/**
	 * Getter for the {@link Emotion} of happiness.
	 * 
	 * @return {@link Emotion} of happiness
	 */
	public function getHappiness() {
		$value = new Eklekt_Emotion(0.0, Eklekt_Emotion.HAPPINESS);
		foreach ($this->emotions as $e) {
			if ($e->getType() == Eklekt_Emotion.HAPPINESS) {
				$value = $e;
			}
		}
		return $value;
	}

	/**
	 * Getter for the happiness weight
	 * 
	 * @return double representing the happiness weight
	 */
	public function getHappinessWeight() {
		return $this->getHappiness()->getWeight();
	}

	/**
	 * Getter for the {@link Emotion} of sadness.
	 * 
	 * @return {@link Emotion} of sadness
	 */
	public function getSadness() {
		$value = new Eklekt_Emotion(0.0, Eklekt_Emotion.SADNESS);
		foreach ($this->emotions as $e) {
			if ($e->getType() == Eklekt_Emotion.SADNESS) {
				$value = $e;
			}
		}
		return $value;
	}

	/**
	 * Getter for the sadness weight
	 * 
	 * @return double representing the sadness weight
	 */
	public function getSadnessWeight() {
		return $this->getSadness()->getWeight();
	}

	/**
	 * Getter for the {@link Emotion} of fear.
	 * 
	 * @return {@link Emotion} of fear
	 */
	public function getFear() {
		$value = new Eklekt_Emotion(0.0, Eklekt_Emotion.FEAR);
		foreach ($this->emotions as $e) {
			if ($e->getType() == Eklekt_Emotion.FEAR) {
				$value = $e;
			}
		}
		return $value;
	}

	/**
	 * Getter for the fear weight
	 * 
	 * @return double representing the fear weight
	 */
	public function getFearWeight() {
		return getFear()->getWeight();
	}

	/**
	 * Getter for the {@link Emotion} of anger.
	 * 
	 * @return {@link Emotion} of anger
	 */
	public function getAnger() {
		$value = new Eklekt_Emotion(0.0, Eklekt_Emotion.ANGER);
		foreach ($this->emotions as $e) {
			if ($e->getType() == Eklekt_Emotion.ANGER) {
				$value = $e;
			}
		}
		return $value;
	}

	/**
	 * Getter for the anger weight
	 * 
	 * @return double representing the anger weight
	 */
	public function getAngerWeight() {
		return $this->getAnger()->getWeight();
	}

	/**
	 * Getter for the {@link Emotion} of disgust.
	 * 
	 * @return {@link Emotion} of disgust
	 */
	public function getDisgust() {
		$value = new Eklekt_Emotion(0.0, Eklekt_Emotion.DISGUST);
		foreach ($this->emotions as $e) {
			if ($e->getType() == Eklekt_Emotion.DISGUST) {
				$value = $e;
			}
		}
		return $value;
	}

	/**
	 * Getter for the disgust weight
	 * 
	 * @return double representing the disgust weight
	 */
	public function getDisgustWeight() {
		return $this->getDisgust()->getWeight();
	}

	/**
	 * Getter for the {@link Emotion} of surprise
	 * 
	 * @return {@link Emotion} of surprise
	 */
	public function getSurprise() {
		$value = new Eklekt_Emotion(0.0, Eklekt_Emotion.SURPRISE);
		foreach ($this->emotions as $e) {
			if ($e->getType() == Eklekt_Emotion.SURPRISE) {
				$value = $e;
			}
		}
		return $value;
	}

	/**
	 * Getter for the surprise weight
	 * 
	 * @return double representing the surprise weight
	 */
	public function getSurpriseWeight() {
		return $this->getSurprise()->getWeight();
	}

	/**
	 * Getter for the previous {@link EmotionalState}
	 * 
	 * @return previous {@link EmotionalState}
	 */
	public function getPrevious() {
		return $this->previous;
	}

	/**
	 * Setter for the previous {@link EmotionalState}
	 * 
	 * @param previous
	 *            previous {@link EmotionalState}
	 */
	public function setPrevious($previous) {
		$this->previous = $previous;
	}

	/**
	 * Getter for the emotional valence
	 * 
	 * @return emotional valence
	 */
	public function getValence() {
		return $this->valence;
	}

	/**
	 * Getter for the general emotional weight
	 * 
	 * @return general emotional weight
	 */
	public function getGeneralWeight() {
		return $this->generalWeight;
	}

	/**
	 * Transforms emotional data into a descriptional sentence ('toString'
	 * method)
	 * 
	 * @return String description of a emotinal data
	 */
	public function __toString() {
		return "Text: " . $this->text . "\nGeneral weight: " . $this->generalWeight
				. "\nValence: " . $this->valence . "\nHappiness weight: "
				. $this->getHappinessWeight() . "\nSadness weight: "
				. $this->getSadnessWeight() . "\nAnger weight: " . $this->getAngerWeight()
				. "\nFear weight: " . $this->getFearWeight() . "\nDisgust weight: "
				. $this->getDisgustWeight() . "\nSurprise weight: "
				. $this->getSurpriseWeight() . "\n";
	}

}
