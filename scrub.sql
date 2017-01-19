-- Scrub a database to remove details of puzzles and other critical information.
-- I'm hoping to be able to:
--   - load a production DB dump into development
--   - run this script:
--      mysql database < scrub.sql
--   - debug things as they are in production without reading any puzzles

-- Sanitize emails.
UPDATE `users` SET email=CONCAT(username, '@example.com')
WHERE email NOT LIKE '%castleblack.net';

-- Replace passwords in the users table
UPDATE `users` SET password=AES_ENCRYPT(UUID(), UUID())
WHERE email NOT LIKE '%castleblack.net';

-- Create a generator for random text
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES';

DELIMITER //
DROP FUNCTION IF EXISTS str_random_lipsum;
//

CREATE FUNCTION str_random_lipsum(p_max_words SMALLINT
                                 ,p_min_words SMALLINT
                                 ,p_start_with_lipsum TINYINT(1)
                                 )
    RETURNS VARCHAR(10000)
    NO SQL
    BEGIN
    /**
    * String function. Returns a random Lorum Ipsum string of nn words
    * <br>
    * %author Ronald Speelman
    * %version 1.0
    * Example usage:
    * SELECT str_random_lipsum(5,NULL,NULL) AS fiveWordsExactly;
    * SELECT str_random_lipsum(10,5,0) AS five-tenWords;
    * SELECT str_random_lipsum(50,10,1) AS startWithLorumIpsum;
    * See more complex examples and a description on www.moinne.com/blog/ronald
    *
    * %param p_max_words         Number: the maximum amount of words, if no
    *                                    min_words are provided this will be the
    *                                    exaxt amount of words in the result
    *                                    Default = 50
    * %param p_min_words         Number: the minimum amount of words in the
    *                                    result, By providing the parameter, you provide a range
    *                                    Default = 0
    * %param p_start_with_lipsum Boolean:if "1" the string will start with
    *                                    'Lorum ipsum dolor sit amet.', Default = 0
    * %return String
    */

        DECLARE v_max_words SMALLINT DEFAULT 50;
        DECLARE v_random_item SMALLINT DEFAULT 0;
        DECLARE v_random_word VARCHAR(25) DEFAULT '';
        DECLARE v_start_with_lipsum TINYINT DEFAULT 0;
        DECLARE v_result VARCHAR(10000) DEFAULT '';
        DECLARE v_iter INT DEFAULT 1;
        DECLARE v_text_lipsum VARCHAR(1500) DEFAULT 'a ac accumsan ad adipiscing aenean aliquam aliquet amet ante aptent arcu at auctor augue bibendum blandit class commodo condimentum congue consectetuer consequat conubia convallis cras cubilia cum curabitur curae; cursus dapibus diam dictum dignissim dis dolor donec dui duis egestas eget eleifend elementum elit enim erat eros est et etiam eu euismod facilisi facilisis fames faucibus felis fermentum feugiat fringilla fusce gravida habitant hendrerit hymenaeos iaculis id imperdiet in inceptos integer interdum ipsum justo lacinia lacus laoreet lectus leo libero ligula litora lobortis lorem luctus maecenas magna magnis malesuada massa mattis mauris metus mi molestie mollis montes morbi mus nam nascetur natoque nec neque netus nibh nisi nisl non nonummy nostra nulla nullam nunc odio orci ornare parturient pede pellentesque penatibus per pharetra phasellus placerat porta porttitor posuere praesent pretium primis proin pulvinar purus quam quis quisque rhoncus ridiculus risus rutrum sagittis sapien scelerisque sed sem semper senectus sit sociis sociosqu sodales sollicitudin suscipit suspendisse taciti tellus tempor tempus tincidunt torquent tortor tristique turpis ullamcorper ultrices ultricies urna ut varius vehicula vel velit venenatis vestibulum vitae vivamus viverra volutpat vulputate';
        DECLARE v_text_lipsum_wordcount INT DEFAULT 180;
        DECLARE v_sentence_wordcount INT DEFAULT 0;
        DECLARE v_sentence_start BOOLEAN DEFAULT 1;
        DECLARE v_sentence_end BOOLEAN DEFAULT 0;
        DECLARE v_sentence_length TINYINT DEFAULT 9;

        SET v_max_words := COALESCE(p_max_words, v_max_words);
        SET v_start_with_lipsum := COALESCE(p_start_with_lipsum , v_start_with_lipsum);

        IF p_min_words IS NOT NULL THEN
            SET v_max_words := FLOOR(p_min_words + (RAND() * (v_max_words - p_min_words)));
        END IF;

        IF v_max_words < v_sentence_length THEN
            SET v_sentence_length := v_max_words;
        END IF;

        IF p_start_with_lipsum = 1 THEN
            SET v_result := CONCAT(v_result,'Lorem ipsum dolor sit amet.');
            SET v_max_words := v_max_words - 5;
        END IF;

        WHILE v_iter <= v_max_words DO
            SET v_random_item := FLOOR(1 + (RAND() * v_text_lipsum_wordcount));
            SET v_random_word := REPLACE(SUBSTRING(SUBSTRING_INDEX(v_text_lipsum, ' ' ,v_random_item),
                                           CHAR_LENGTH(SUBSTRING_INDEX(v_text_lipsum,' ', v_random_item -1)) + 1),
                                           ' ', '');

            SET v_sentence_wordcount := v_sentence_wordcount + 1;
            IF v_sentence_wordcount = v_sentence_length THEN
                SET v_sentence_end := 1 ;
            END IF;

            IF v_sentence_start = 1 THEN
                SET v_random_word := CONCAT(UPPER(SUBSTRING(v_random_word, 1, 1))
                                            ,LOWER(SUBSTRING(v_random_word FROM 2)));
                SET v_sentence_start := 0 ;
            END IF;

            IF v_sentence_end = 1 THEN
                IF v_iter <> v_max_words THEN
                    SET v_random_word := CONCAT(v_random_word, '.');
                END IF;
                SET v_sentence_length := FLOOR(9 + (RAND() * 7));
                SET v_sentence_end := 0 ;
                SET v_sentence_start := 1 ;
                SET v_sentence_wordcount := 0 ;
            END IF;

            SET v_result := CONCAT(v_result,' ', v_random_word);
            SET v_iter := v_iter + 1;
        END WHILE;

        RETURN TRIM(CONCAT(v_result,'.'));
END;
//
DELIMITER ;

SET SQL_MODE=@OLD_SQL_MODE;


-- replace the answers and attempts
UPDATE answer_attempts SET answer=UPPER(str_random_lipsum(3, 1, 0));
UPDATE answers SET answer=UPPER(str_random_lipsum(3, 1, 0));
-- replace the comments with comments of an equal number of random words
UPDATE comments
SET comment=str_random_lipsum(LENGTH(comment) - LENGTH(REPLACE(comment, ' ', '')) + 1, NULL, 0);

DELETE FROM email_outbox;

-- replace the puzzle text with text of an equal number of random words
UPDATE puzzles SET
summary=str_random_lipsum(LENGTH(summary) - LENGTH(REPLACE(summary, ' ', '')) + 1, NULL, 0),
description=str_random_lipsum(LENGTH(description) - LENGTH(REPLACE(description, ' ', '')) + 1, NULL, 0),
title=str_random_lipsum(LENGTH(title) - LENGTH(REPLACE(title, ' ', '')) + 1, NULL, 0),
notes=str_random_lipsum(LENGTH(notes) - LENGTH(REPLACE(notes, ' ', '')) + 1, NULL, 0),
editor_notes=str_random_lipsum(LENGTH(editor_notes) - LENGTH(REPLACE(editor_notes, ' ', '')) + 1, NULL, 0),
wikipage=str_random_lipsum(LENGTH(wikipage) - LENGTH(REPLACE(wikipage, ' ', '')) + 1, NULL, 0),
credits=str_random_lipsum(LENGTH(credits) - LENGTH(REPLACE(credits, ' ', '')) + 1, NULL, 0),
runtime_info=str_random_lipsum(LENGTH(runtime_info) - LENGTH(REPLACE(runtime_info, ' ', '')) + 1, NULL, 0);

UPDATE rounds SET
answer=str_random_lipsum(3, 1, 0),
name=str_random_lipsum(LENGTH(name) - LENGTH(REPLACE(name, ' ', '')) + 1, NULL, 0);

UPDATE testing_feedback SET
how_long=str_random_lipsum(LENGTH(how_long) - LENGTH(REPLACE(how_long, ' ', '')) + 1, NULL, 0),
tried=str_random_lipsum(LENGTH(tried) - LENGTH(REPLACE(tried, ' ', '')) + 1, NULL, 0),
liked=str_random_lipsum(LENGTH(liked) - LENGTH(REPLACE(liked, ' ', '')) + 1, NULL, 0),
breakthrough=str_random_lipsum(LENGTH(breakthrough) - LENGTH(REPLACE(breakthrough, ' ', '')) + 1, NULL, 0),
skills=str_random_lipsum(LENGTH(skills) - LENGTH(REPLACE(skills, ' ', '')) + 1, NULL, 0),
when_return=str_random_lipsum(LENGTH(when_return) - LENGTH(REPLACE(when_return, ' ', '')) + 1, NULL, 0),
done = 0,
fun = 0,
difficulty = 0;

UPDATE testsolve_requests SET
notes=str_random_lipsum(LENGTH(notes) - LENGTH(REPLACE(notes, ' ', '')) + 1, NULL, 0);

-- That's the most critical stuff; now do some trivial stuff for completeness.

UPDATE motds SET message=str_random_lipsum(LENGTH(message) - LENGTH(REPLACE(message, ' ', '')) + 1, NULL, 0);

UPDATE user_info_values SET
value=str_random_lipsum(LENGTH(value) - LENGTH(REPLACE(value, ' ', '')) + 1, NULL, 0);

