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
 * Utility class for some heuristic alghoritms used for text processing.
 * 
 * @author Uros Krcadinac email: uros@krcadinac.com
 * @version 1.0
 */
class Eklekt_Emotion_Utility_Heuristics {

	/**
	 * Computes emotiocon qoef for the sentence. Qoef is based on number of
	 * important chars in an emotion (e.g. ')' in ':)))))' ).
	 * 
	 * @param sentence
	 *            {@link String} representing the sentence
	 * @return double value of the emoticon coef
	 * @throws IOException
	 */
	public static function computeEmoticonCoefForSentence($sentence) {
	    $emoticons = Eklekt_Emotion_Utility_Lexical::getInstance()->getEmoticonWords($sentence);
		$value = 1.0;
		foreach ($emoticons as $emot) {
			$emotWord = $emot->getWord();
			$value *= 1.0 + (0.2 * substr_count($sentence, $emotWord[count($emotWord) - 1]));
		}
		return value;
	}

	
	
	/**
	 * Computes emoticon qoef for the word. Qoef is based on number of important
	 * chars in an emotion (e.g. ')' in ':)))))' ).
	 * 
	 * @param word
	 *            {@link String} representing the word
	 * @param emoticon
	 *            {@link AffectWord} representing the emoticon
	 * @return double value of the emoticon qoef
	 */
	public static function computeEmoticonCoef($word, $emoticon) {
		if ($emoticon->startsWithEmoticon()) {
			$emotiveWord = $emoticon->getWord();

			return 1.0 + (0.2 * substr_count($word, $emotiveWord[count($emotiveWord) - 1]));
		} else {
			return 1.0;
		}
	}

	/**
	 * Returns true if sentence has negation in it.
	 * 
	 * @param sentence
	 *            {@link String} representing the sentence
	 * @return boolean, true if sentence has negation in it
	 * @throws IOException
	 */
	public static function hasNegation($sentence) {
	    return Eklekt_Emotion_Utility_Lexical::getInstance()->hasNegation($sentence);
	}

	/**
	 * Computes the intensity modifier based on the word.
	 * 
	 * @param word
	 *            {@link String} representing the word
	 * @return double representing the modifier
	 * @throws IOException
	 */
	public static function computeModifier($word) {
		if (Eklekt_Emotion_Utility_Heuristics::isIntensityModifier($word))
			return 1.5;
		else
			return 1.0;
	}

	/**
	 * Computes the upper case qoeficient.
	 * 
	 * @param word {@link String} representing the word
	 * @return double representing the upper case qoeficient
	 */
	public static function computeCapsLockQoef($word) {
		if (Eklekt_Emotion_Utility_Heuristics::isCapsLock($word))
			return 1.5;
		else
			return 1.0;
	}
	
	/**
	 * Computes the exclamination qoef -- function of a number of '!' chars in a sentence.
	 * 
	 * @param text {@link String} representing the sentence 
	 * @return double representing the exclamination qoef
	 */
	
	public static function computeExclaminationQoef($text) {
	    //TODO: Translate
		return 1.0 + (0.2 * substr_count($text, '!'));
	}

	private static function isCapsLock($word) {
	    return ($word === strtoupper($word));
	}

	private static function isIntensityModifier($word) {
		return Eklekt_Emotion_Utility_Lexical::getInstance()->isIntensityModifier($word);
	}

	
}
