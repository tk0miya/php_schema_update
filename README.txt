
�� schema_update: �f�[�^�x�[�X��`�X�V�c�[��

schema_update �̓f�[�^�x�[�X�̍X�V���ȈՉ�����c�[���ł��B

����܂Ńf�[�^�x�[�X��`�̍X�V�͍��� SQL ��K�X�K�p���Ă��܂������A
�J������X�e�[�W���O���A�{�Ԋ��Ȃǂ̂������̃T�[�o��
�X�L�[�}�̏�Ԃ𐳂����c�����Ȃ��獷�� SQL ��K�p����K�v������A
�Ǘ�����₱�����Ȃ錴���ƂȂ��Ă��܂����B

schema_update �ł́A��`���X�V����K�v����������s�x�A
�p�b�`���ɍ��� SQL ��ǉ����Ă����������̂��Ă��܂��B
�܂��A�f�[�^�x�[�X��ɃX�L�[�}�̃o�[�W��������ێ����Ă��܂��̂ŁA
�ǂ́u�����v�܂œK�p����Ă���̂��������I�ɔ��ʂ��A
���� SQL ��]�����Aschema_update �����s���邾���łǂ̊����ꗥ��
�ŐV�̃f�[�^�x�[�X��`�ɍX�V���邱�Ƃ��ł��܂��B


���p����ۂɂ͈ȉ��̃c�[�����K�v�ł��B

�EPHP 5.x
�EDB �h���C�o
  �EPHP �{�̂� DB �h���C�o
  �EPear MDB2
  �E�ȉ��̃R�}���h�ŃC���X�g�[�������
      pear install mdb2

�� �g����

(1) �Ώۂ̃f�[�^�x�[�X�ɑ΂��Adb_schema �e�[�u�����쐬���ĉ������B
    (�Q�l: init_mysql.sql)

(2) config.php ��K���ɕҏW�� schema_update.php �Ɠ����f�B���N�g���ɐݒu���ĉ������B

(3) ���� SQL �� config.php �� $sql_dir �Ŏw�肵���f�B���N�g���ɒǉ����ĉ������B
    ���� SQL �� [�ԍ�]_....sql �Ƃ����t�@�C�����Ƃ��ĉ������B
    (��: 001_create_table.sql)

(4) �R�}���h�v�����v�g����ȉ������s���ĉ������B

    % php schema_update.php

