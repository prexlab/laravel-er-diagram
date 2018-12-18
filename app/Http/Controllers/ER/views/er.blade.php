<?='<?xml version="1.0" encoding="utf-8" ?>'; ?>

<sql>
    <datatypes db="mysql">
        <group color="rgb(238,238,170)" label="Numeric">
            <type label="Integer" quote="" sql="INTEGER" length="0"/>
            <type label="TINYINT" quote="" sql="TINYINT" length="0"/>
            <type label="SMALLINT" quote="" sql="SMALLINT" length="0"/>
            <type label="MEDIUMINT" quote="" sql="MEDIUMINT" length="0"/>
            <type label="INT" quote="" sql="INT" length="0"/>
            <type label="BIGINT" quote="" sql="BIGINT" length="0"/>
            <type label="Decimal" quote="" sql="DECIMAL" length="1" re="DEC"/>
            <type label="Single precision" quote="" sql="FLOAT" length="0"/>
            <type label="Double precision" quote="" sql="DOUBLE" length="0" re="DOUBLE"/>
        </group>

        <group color="rgb(255,200,200)" label="Character">
            <type label="Char" quote="'" sql="CHAR" length="1"/>
            <type label="Varchar" quote="'" sql="VARCHAR" length="1"/>
            <type label="Text" quote="'" sql="MEDIUMTEXT" length="0" re="TEXT"/>
            <type label="Binary" quote="'" sql="BINARY" length="1"/>
            <type label="Varbinary" quote="'" sql="VARBINARY" length="1"/>
            <type label="BLOB" quote="'" sql="BLOB" length="0" re="BLOB"/>
        </group>

        <group color="rgb(200,255,200)" label="Date &amp; Time">
            <type label="Date" quote="'" sql="DATE" length="0"/>
            <type label="Time" quote="'" sql="TIME" length="0"/>
            <type label="Datetime" quote="'" sql="DATETIME" length="0"/>
            <type label="Year" quote="" sql="YEAR" length="0"/>
            <type label="Timestamp" quote="'" sql="TIMESTAMP" length="0"/>
        </group>

        <group color="rgb(200,200,255)" label="Miscellaneous">
            <type label="ENUM" quote="" sql="ENUM" length="1"/>
            <type label="SET" quote="" sql="SET" length="1"/>
            <type label="Bit" quote="" sql="bit" length="0"/>
        </group>
    </datatypes>

    @foreach($tables as $name => $table)

        <?php
        $x = $depth[$name] * 600;
        $y = call_user_func(function ($name, $tables, $count, $depth, $rowHeight, $headerHeight, $spaceHeight) {
            $height = 0;

            $key = array_search($name, $count[$depth[$name]]);
            for ($i = 0; $i < $key; $i++) {
                $nm = $count[$depth[$name]][$i];
                $height += sizeof($tables[$nm]) * $rowHeight + $headerHeight;
            }

            return $height + $spaceHeight;
        }, $name, $tables, $count, $depth, 30, 60, 50);

        ?>
        <table x="{{ 500 + $x }}" y="{{ 500 + $y }}" name="{{ $name }}">
            @foreach($table as $row)
                <row name="{{$row['column']}}" >
                    <datatype>{{$row['type']}}</datatype>
                    @if(!empty($row['relation']))
                        <relation
                            table="{{ array_get($row, 'relation.table') }}"
                            row="{{ array_get($row, 'relation.column') ?: 'id' }}"
                        />
                    <comment>{{$row['relation']['type']}}</comment>
                    @endif
                </row>
            @endforeach
        </table>
    @endforeach

</sql>