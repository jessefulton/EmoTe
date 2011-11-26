#### This project is no longer being supported. I have begun a new, similar project: [emotivetext.com](https://github.com/jessefulton/emotivetext.com)


These classes were built to be used with the Zend framework. Here is an example of usage:

               $text = stripslashes(htmlspecialchars_decode($this->_getParam("text")));
               $empathScope = Eklekt_Emotion_Empathyscope::getInstance();
               $result = $empathScope->feel($text);
               echo json_encode($result);
               exit();

It should work outside of the Zend Framework - just create an instance of the Empathyscope object and pass it the text you want to evaluate.