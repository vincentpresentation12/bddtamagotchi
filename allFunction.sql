    DELIMITER //
-- Procédure avec passage par valeur pour compte
CREATE PROCEDURE CREATE_ACCOUNT(IN name VARCHAR(255))
BEGIN
    INSERT INTO accounts (name) VALUES (name);
END//

    DELIMITER //
-- Procédure  tamagotchi
CREATE PROCEDURE CREATE_TAMAGOTCHI(IN name VARCHAR(255),IN account_id INT)
BEGIN
    INSERT INTO tamagotchi (name, account_id) VALUES (name, account_id);
END//

DELIMITER //

    -- procédure manger
CREATE PROCEDURE EAT(IN tamagotchi_id INT)
BEGIN
    INSERT INTO actions (name, tamagotchi_id) VALUES ('eat', tamagotchi_id);
END//

    DELIMITER //
      -- procédure boire
CREATE PROCEDURE DRINK(IN tamagotchi_id INT)
BEGIN
    INSERT INTO actions (name, tamagotchi_id) VALUES ('drink', tamagotchi_id);
END//

    DELIMITER //

      -- procédure dormir
CREATE PROCEDURE BEDTIME(IN tamagotchi_id INT)
BEGIN
    INSERT INTO actions (name, tamagotchi_id) VALUES ('bedtime', tamagotchi_id);
END//

    DELIMITER //

        -- procédure enjoy
CREATE PROCEDURE ENJOY(IN tamagotchi_id INT)
BEGIN
    INSERT INTO actions (name, tamagotchi_id) VALUES ('enjoy', tamagotchi_id);
END//

    DELIMITER //

      -- fonction niveau all 10 action
CREATE FUNCTION LEVEL(IN tamagotchi_id INT) RETURNS INT
BEGIN
    DECLARE level INT;
    SELECT FLOOR(COUNT(*)/10)+1  INTO level FROM actions WHERE tamagotchi_id = tamagotchi_id ;
    RETURN level;
END//

    DELIMITER //

    --fonction vivant
CREATE FUNCTION IS_ALIVE(IN tamagotchi_id INT) RETURNS BOOLEAN
BEGIN
    DECLARE alive BOOLEAN;
        SELECT COUNT(*)  FROM deaths WHERE tamagotchi_id = tamagotchi_id ;
    IF alive = 0 THEN
        RETURN TRUE;
    ELSE
        RETURN FALSE;
    END IF;
END//

    DELIMITER //

--trigger about actions
CREATE TRIGGER actions_trigger AFTER INSERT ON actions
FOR EACH ROW
BEGIN
    IF NEW.name = 'eat' THEN
        UPDATE tamagotchis SET hunger = hunger + 30 + LEVEL(NEW.tamagotchi_id)-1 WHERE id = NEW.tamagotchi_id;
        UPDATE tamagotchis SET thirst = thirst - 10 - LEVEL(NEW.tamagotchi_id)+1 WHERE id = NEW.tamagotchi_id;
        UPDATE tamagotchis SET sleep = sleep - 5 - LEVEL(NEW.tamagotchi_id)+1 WHERE id = NEW.tamagotchi_id;
        UPDATE tamagotchis SET boredom = boredom - 5 - LEVEL(NEW.tamagotchi_id)+1 WHERE id = NEW.tamagotchi_id;
    ELSEIF NEW.name = 'drink' THEN
        UPDATE tamagotchis SET thirst = thirst + 30 + LEVEL(NEW.tamagotchi_id)-1 WHERE id = NEW.tamagotchi_id;
        UPDATE tamagotchis SET hunger = hunger - 10 - LEVEL(NEW.tamagotchi_id)+1 WHERE id = NEW.tamagotchi_id;
        UPDATE tamagotchis SET sleep = sleep - 5 - LEVEL(NEW.tamagotchi_id)+1 WHERE id = NEW.tamagotchi_id;
        UPDATE tamagotchis SET boredom = boredom - 5 - LEVEL(NEW.tamagotchi_id)+1 WHERE id = NEW.tamagotchi_id;
    ELSEIF NEW.name = 'bedtime' THEN
        UPDATE tamagotchis SET sleep = sleep + 30 + LEVEL(NEW.tamagotchi_id)-1 WHERE id = NEW.tamagotchi_id;
        UPDATE tamagotchis SET thirst = thirst - 15 - LEVEL(NEW.tamagotchi_id)+1 WHERE id = NEW.tamagotchi_id;
        UPDATE tamagotchis SET hunger = hunger - 10 - LEVEL(NEW.tamagotchi_id)+1 WHERE id = NEW.tamagotchi_id;
        UPDATE tamagotchis SET boredom = boredom - 15 - LEVEL(NEW.tamagotchi_id)+1 WHERE id = NEW.tamagotchi_id;
    ELSEIF NEW.name = 'enjoy' THEN
        UPDATE tamagotchis SET boredom = boredom + 15 + LEVEL(NEW.tamagotchi_id)-1 WHERE id = NEW.tamagotchi_id;
        UPDATE tamagotchis SET thirst = thirst - 5 - LEVEL(NEW.tamagotchi_id)+1 WHERE id = NEW.tamagotchi_id;
        UPDATE tamagotchis SET sleep = sleep - 5 - LEVEL(NEW.tamagotchi_id)+1 WHERE id = NEW.tamagotchi_id;
        UPDATE tamagotchis SET hunger = hunger - 5 - LEVEL(NEW.tamagotchi_id)+1 WHERE id = NEW.tamagotchi_id;
    END IF;
    SELECT hunger AS hunger FROM tamagotchis WHERE id = NEW.tamagotchi_id;
    SELECT thirst AS thirst FROM tamagotchis WHERE id = NEW.tamagotchi_id;
    SELECT sleep AS sleep FROM tamagotchis WHERE id = NEW.tamagotchi_id;
    SELECT boredom AS boredom FROM tamagotchis WHERE id = NEW.tamagotchi_id;
       IF hunger <= 0 OR thirst <= 0 OR sleep <= 0 OR boredom <= 0 THEN
            INSERT INTO deaths (tamagotchi_id) VALUES (NEW.tamagotchi_id);
        END IF;
END//



